# MyInventory v2 — Project Status

**Last updated:** 11 September 2026
**Location:** `C:\laragon\www\MyInventoryv2\myinventoryv2`
**Stack:** Laravel 13.17 · PHP 8.4.12 · MySQL · Blade + Alpine.js + Tailwind CSS 3 · Vite 8
**Local URL:** `http://myinventoryv2.test` (Laragon)
**Local mail testing:** Mailpit (`http://localhost:8025`) via `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`

**Context for whoever picks this up (including a fresh AI chat session):** this is a real in-house side project for a cafe, built by a fresh graduate aiming to become a junior developer. The priority is not just "finish the app" — it's understanding *why* each decision is made (architecture, security, database design, testing). Mentoring style: explain reasoning, don't just hand over final code. Guide toward the solution before giving the full implementation. Point out bugs/bad practices/security issues with the "why," not just the "what." Avoid over-engineering — this is a small cafe system, not an enterprise product.

---

## 1. Where the project stands

The **data layer, authentication, Category/Item CRUD, stock movement flow, and User Management (roles) are now complete and working**. The application has a working three-tier role hierarchy (superadmin / admin / staff) with policy-based authorization, and an invite-based staff onboarding flow (no plaintext passwords ever sent).

What is **not yet done**: automated tests for anything beyond Breeze's own auth scaffolding, low-stock dashboard alerts, transaction history views, and various polish items listed in Section 3.

Rough completion: **~65%** (foundation, auth, roles, and core CRUD/stock-flow done; dashboard, reporting, and testing still pending).

---

## 2. What has been done

### Application foundation
- [x] Laravel 13.17 project running under Laragon
- [x] `.env` configured — MySQL connection, `myinventory` database
- [x] Mail switched from `log` driver to **Mailpit** (SMTP, `127.0.0.1:1025`) for realistic local email testing
- [x] Frontend toolchain: Vite 8 + Tailwind 3 + Alpine.js
- [x] Git repository initialised

### Authentication & user account (Laravel Breeze 2.4, Blade stack)
- [x] Register, login, logout, password reset, email verification scaffolding
- [x] Profile page, full UI component library, Breeze's own feature tests

### Database schema
- [x] `categories`, `items`, `inventory_transactions` tables — all migrated
- [x] `users.role` enum — **now supports `superadmin` / `admin` / `staff`**
      (see [migration](database/migrations/2026_09_11_034008_add_superadmin_to_users_role_enum.php)
      — added via a **new** migration using raw `ALTER TABLE`, since the original
      `users` migration had already run; editing an already-run migration would
      cause schema drift between environments)

### Seeders
- [x] `UserSeeder` — **refactored to use `User::create()` instead of `User::insert()`**,
      so timestamps, password hashing (via model cast), and `$fillable`/`#[Fillable]`
      protection are all properly respected. Seeds `Ahmad` (superadmin) and
      `Shahrul` (staff).
- [x] `CategorySeeder` — still uses `Category::insert()` (minor inconsistency,
      see Section 3 cleanups)

### Inventory models
- [x] `Category`, `Item`, `InventoryTransaction` — `$fillable`, `casts()`, `HasFactory` all in place
- [x] `User` model — `#[Fillable]` includes `role`; helper methods added:
      - `isSuperAdmin()`: strictly `role === 'superadmin'`
      - `isAdmin()`: `role` is `'admin'` **or** `'superadmin'` (i.e. "admin-level access or higher")
      - `isStaff()`: strictly `role === 'staff'`

### Category & Item CRUD — fully built
- [x] Controllers, routes, views, Form Requests for both Category and Item
- [x] `EnsureUserIsAdmin` middleware protecting `categories`/`users` routes

### Stock movement flow — fully built
- [x] `StockMovementService` — records transactions and keeps `items.current_stock`
      in sync **inside a DB transaction with row locking** (`lockForUpdate()`),
      preventing race conditions on concurrent stock updates
- [x] `InsufficientStockException` — custom exception for stock-out attempts that
      would push stock negative
- [x] `TransactionType` enum (`In` / `Out` / `Adjustment`) with `label()` method
- [x] Views for stock in/out/adjustment forms

### User Management — fully built (this session's main feature)
- [x] **Three-tier role hierarchy**: `superadmin` (fixed, immutable, exactly one,
      created only via Tinker/seeder — never through the UI) → `admin` (can manage
      staff, can promote staff to admin, cannot demote/delete themselves) → `staff`
      (no admin access)
- [x] `UserPolicy` — centralizes all "who can do what to whom" logic:
      - `before()` hook grants superadmin blanket access to every ability
      - `viewAny`, `create` — admin-level only
      - `delete` — superadmin can delete anyone except superadmin; admin can only
        delete staff, never themselves, never other admins
      - `updateRole` — superadmin's role can never be changed via UI; admin cannot
        demote themselves
- [x] `StoreUserRequest`, `UpdateUserRoleRequest` — Form Requests that call the
      Policy in `authorize()`, keeping validation and authorization out of the controller
- [x] `UserController` — `index`, `create`, `store`, `updateRole`, `destroy`
- [x] **Invite-based onboarding (no plaintext passwords ever sent):**
      - New staff/admin accounts are created with `Hash::make(Str::random(40))` —
        a password nobody (not even the admin who created it) ever sees
      - `Password::createToken($user)` generates a reset token using Laravel's
        existing `password_reset_tokens` infrastructure (same table/mechanism as
        "Forgot Password")
      - `WelcomeNewStaffNotification` — a **custom** notification (not the built-in
        `ResetPassword` one) that reuses the same token/link logic but with
        onboarding-appropriate wording ("Welcome! Please set your password")
      - New user clicks the emailed link → lands on the **existing** Breeze
        `reset-password/{token}` flow → sets their own password → logs in
- [x] `users/index.blade.php` — table with inline, auto-submitting role dropdown
      per row (`onchange="this.form.submit()"`), wrapped in a JS `confirm()` dialog;
      dropdown is hidden for the logged-in admin's own row and for superadmin rows
      (shown as a static badge instead) — **UI convenience only**, the real
      enforcement is server-side via `UserPolicy`
- [x] `users/create.blade.php` — create form with role dropdown (`admin`/`staff`
      only — `superadmin` is never an option, enforced both by the dropdown and
      by server-side validation `in:admin,staff`)
- [x] Navigation — "Users" link added alongside "Categories", gated by `isAdmin()`
      (which already covers superadmin, since `isAdmin()` means "admin or higher")

### Base Controller fix
- [x] `app/Http/Controllers/Controller.php` — added `use AuthorizesRequests;`
      trait. **Note for future reference:** Laravel 11+ skeleton projects ship
      with an intentionally empty base `Controller` (no auto-included traits,
      unlike Laravel 8 and earlier). Any use of `$this->authorize(...)` requires
      this trait to be explicitly added.

---

## 3. What has NOT been done

### Testing — biggest gap right now
- [ ] **No automated tests exist yet for Category, Item, StockMovement, or
      UserManagement features** — only Breeze's own auth tests exist
      (`tests/Feature/Auth/*`, `tests/Feature/ProfileTest.php`)
- [ ] Specifically needed for `UserManagement` (good first tests to write solo,
      without step-by-step guidance, as a self-check exercise):
  - Staff cannot access `/users` (expect 403)
  - Admin cannot delete themselves
  - Admin cannot delete another admin
  - Admin cannot delete/change role of superadmin
  - Admin *can* delete/promote staff
  - Creating a staff member sends a notification (`Notification::fake()`)
  - New user can complete the password-reset link flow and log in
- [ ] `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` still placeholders
- [ ] Reference material for learning this: official Laravel docs
      (`https://laravel.com/docs/testing`, `.../http-tests`), the existing
      `tests/Feature/Auth/*.php` files in this repo (best reference — same
      stack/conventions), and Laravel Bootcamp (`https://bootcamp.laravel.com`)

### Dashboard & reporting — not started
- [ ] Dashboard still Breeze's placeholder ("You're logged in!") — no counts,
      no low-stock alerts, no recent activity
- [ ] Low-stock detection — `minimum_stock` exists on `items` but nothing
      compares against it outside the index table badge
- [ ] Transaction history — no per-item or global movement log view yet
      (data is being recorded correctly by `StockMovementService`, just not
      displayed anywhere yet)

### User Management — possible future refinements (not urgent)
- [ ] No `ItemSeeder`/`UserFactory` usage yet for realistic bulk test data
- [ ] No rate limiting on the "create staff" action (a rapid double-click could
      send duplicate invite emails) — not critical for a small cafe, worth knowing
      for larger systems
- [ ] No audit log of who created/deleted/changed roles for which user — worth
      considering if this app ever needs to answer "who did this?"

### Not yet started (confirm scope before building)
- [ ] Search, filtering, sorting, pagination refinements on listings
- [ ] Reports / CSV export
- [ ] Item images or attachments
- [ ] Barcode/QR scanning
- [ ] Suppliers, purchase orders, locations/warehouses
- [ ] Soft deletes on any model

### Minor cleanups
- [ ] `CategorySeeder` still uses `Category::insert()` — same NULL-timestamp
      issue that `UserSeeder` used to have; low priority but worth fixing for
      consistency
- [ ] Migration filenames omit conventional `create_` prefix — cosmetic only
- [ ] `README.md` still the stock Laravel readme
- [ ] `@tailwindcss/vite` v4 in `package.json` but unused (project uses Tailwind 3
      PostCSS setup) — harmless, can be removed
- [ ] Some remaining Malay-language comments/strings in files predating this
      session (e.g. `StoreCategoryRequest` comment) — not urgent, but worth a
      pass for consistency if the codebase is meant to go on a public portfolio

---

## 4. Suggested next steps, in order

1. **Write Feature tests for UserManagement solo** (see checklist in Section 3) —
   this is a deliberate skill-building exercise, not just a task. Attempt it
   without step-by-step guidance first; bring back what's written for review.
2. Build the **dashboard** with real figures (item count, low-stock count,
   recent transactions) — good next feature since all the underlying data
   already exists from Category/Item/StockMovement work.
3. Build a **transaction history** listing (per-item and/or global) — the
   `InventoryTransaction` model and data already exist; this is primarily a
   read/display exercise.
4. Once dashboard + history exist, revisit **UserManagement tests** written in
   step 1 and add a few more scenarios if gaps were found.
5. Minor cleanups from Section 3 (seeder consistency, language consistency) —
   good "housekeeping" tasks to practice discipline, not urgent.
6. Only after the above: consider search/filter/pagination polish, exports,
   or other "not yet started" items — confirm actual business need before
   building, to avoid over-engineering a small cafe system.

---

## 5. Key design decisions made this session (for context in a new chat)

- **Superadmin is fixed and immutable**: exactly one, created only via
  Tinker/seeder, never selectable in any UI dropdown, and protected at both
  the `UserPolicy` and Form Request validation layers. Deliberately avoided
  building "ownership transfer" UI (YAGNI) — if ever needed, do it manually.
- **Staff onboarding never sends a real password by email.** New accounts get
  a random, unusable password (`Str::random(40)`, hashed, never exposed).
  Onboarding reuses Laravel's existing password-reset token infrastructure
  (`password_reset_tokens` table, `Password::createToken()`,
  `reset-password/{token}` route) with a custom-worded `Notification` class —
  chosen specifically to avoid rebuilding proven, already-tested infrastructure.
- **Authorization logic lives in `UserPolicy`, not scattered in controllers/views.**
  Controllers stay thin because Form Requests call the Policy in `authorize()`.
  Blade views additionally use `@can(...)` to hide UI elements the user isn't
  allowed to use — but this is UX polish only; the server-side Policy is the
  actual security boundary.
- **`isAdmin()` deliberately means "admin-level access or higher"** (includes
  superadmin), while `isSuperAdmin()` is the strict/exclusive check. This
  caused one moment of confusion this session (navigation link visibility) —
  worth remembering when reading/writing role checks elsewhere in the app.

---

## 6. Handy commands

```bash
composer setup            # install deps, generate key, migrate, build assets
composer dev              # serve + queue worker + vite, all at once
composer test             # run the test suite
php artisan migrate
php artisan migrate:fresh --seed   # rebuild schema and reseed
php artisan migrate:status
vendor/bin/pint           # format code
```

**Caution:** `migrate:fresh`/`migrate:refresh` drop all tables and rebuild from
scratch — this project's data was accidentally lost once this session from an
AI coding tool running such a command unprompted. Always confirm before running
destructive migration commands, and disable auto-run for destructive commands
in any AI coding tool's settings.

## 7. Seeded login credentials

| Email            | Password   | Role       |
| ---------------- | ---------- | ---------- |
| `admin@test.com` | `admin123` | superadmin |
| `user@test.com`  | `user123`  | staff      |

(No seeded `admin`-role account exists by default — create one manually via
Tinker or the UserManagement UI if needed for testing role-specific behavior.)
