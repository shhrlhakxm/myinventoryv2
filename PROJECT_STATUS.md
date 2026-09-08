# MyInventory v2 — Project Status

**Last reviewed:** 8 September 2026
**Location:** `C:\laragon\www\MyInventoryv2\myinventoryv2`
**Stack:** Laravel 13.17 · PHP 8.4.12 · MySQL · Blade + Alpine.js + Tailwind CSS 3 · Vite 8
**Local URL:** `http://myinventoryv2.test` (Laragon)
**Git:** `main` branch, working tree clean — 2 commits (`ad2fcd5` initial, `f8789db` migrations & seeders)

---

## 1. Where the project stands

The **data layer is now complete and live**. All three inventory tables exist, the migrations
have been run against the `myinventory` database, and seed data is in place. The schema
also introduces an `admin` / `staff` role on users.

What is still entirely missing is **everything above the database** — no controllers, no
routes, no views, no form requests. The models are bare relationship stubs without
`$fillable`, so even manual record creation will be rejected.

In short: **the database is ready and populated, but there is still no way to use it through
the browser.**

Rough completion: **~30%** (foundation + schema done, application layer not started).

---

## 2. What has been done

### Application foundation
- [x] Laravel 13.17 project created and running under Laragon
- [x] `.env` configured — MySQL connection pointing at the `myinventory` database
- [x] `APP_KEY` generated; `APP_URL` set to `http://myinventoryv2.test`
- [x] Session, cache and queue drivers all set to `database`
- [x] Mail driver set to `log` (fine for local dev; needs a real mailer before production)
- [x] Composer helper scripts available — `composer setup`, `composer dev`, `composer test`
- [x] Frontend toolchain wired up: Vite 8 + Tailwind 3 + Alpine.js, assets compiled
      (`public/build/` exists)
- [x] Code style tooling installed — Laravel Pint
- [x] **Git repository initialised**, with two meaningful commits

### Authentication & user account (Laravel Breeze 2.4, Blade stack)
- [x] Register, login, logout
- [x] Forgot password / password reset flow
- [x] Password confirmation screen
- [x] Email verification scaffolding (routes, controllers, views)
- [x] Profile page — update name/email, change password, delete account
- [x] Full UI component library — buttons, inputs, labels, modal, dropdown, nav links
- [x] App and guest layouts plus the navigation bar
- [x] Breeze's own feature tests present (`tests/Feature/Auth/*`, `tests/Feature/ProfileTest.php`)

### Database schema — **all migrations written and run**
- [x] `categories` — [migration](database/migrations/2026_09_08_084339_categories_table.php)
      `name`, `code` (unique, e.g. `P01`), `description` (nullable), timestamps
- [x] `items` — [migration](database/migrations/2026_09_08_084352_items_table.php)
      `category_id` (FK, cascade delete), `name`, `sku` (unique), `current_stock` (default 0),
      `minimum_stock` (default 5), `unit_price` (decimal 10,2), timestamps
- [x] `inventory_transactions` — [migration](database/migrations/2026_09_08_084412_inventory_transactions_table.php)
      `item_id` (FK, cascade), `user_id` (FK, cascade), `type` enum (`in` / `out` / `adjustment`),
      `quantity`, `notes` (nullable), timestamps
- [x] **`role` enum added to `users`** — `admin` / `staff`, defaults to `staff`
      ([users migration](database/migrations/0001_01_01_000000_create_users_table.php#L20))
- [x] Framework tables — `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`
- [x] `php artisan migrate` has been run — all 6 migrations show **Ran** in batch 1

### Seeders
- [x] [UserSeeder.php](database/seeders/UserSeeder.php) — two accounts:
      `admin@test.com` / `admin123` (admin) and `user@test.com` / `user123` (staff)
- [x] [CategorySeeder.php](database/seeders/CategorySeeder.php) — Packaging (`P01`),
      Coffee and Beverages (`CB01`)
- [x] [DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) calls both in order
- [x] Seeders have been run — database currently holds **2 users, 2 categories**

### Inventory models — relationships only
- [x] [Category.php](app/Models/Category.php) — `hasMany(Item)`
- [x] [Item.php](app/Models/Item.php) — `belongsTo(Category)`, `hasMany(InventoryTransaction)`
- [x] [InventoryTransaction.php](app/Models/InventoryTransaction.php) — `belongsTo(User)`,
      `belongsTo(Item)`
- [x] [User.php](app/Models/User.php#L33) — `hasMany(InventoryTransaction)`

---

## 3. What has NOT been done

### Blocking — needed before any CRUD will work
- [ ] **No `$fillable` on `Category`, `Item` or `InventoryTransaction`** — mass assignment
      will throw, so no form can create or update these records. This is the single biggest
      blocker right now.
- [ ] **`User`'s `#[Fillable]` omits `role`** ([User.php:13](app/Models/User.php#L13)) — the
      column exists but cannot be mass-assigned, so new users always fall back to the `staff`
      default and there is no way to create an admin through a form.
- [ ] No `casts()` on the inventory models — `unit_price` returns a string rather than a
      decimal, and `type` is not cast to an enum
- [ ] No `HasFactory` trait on the three inventory models
- [ ] No factories — `CategoryFactory`, `ItemFactory`, `InventoryTransactionFactory`

### Application layer — not started
- [ ] **No controllers** for Category, Item or InventoryTransaction
- [ ] **No routes** — [routes/web.php](routes/web.php) still only has `/`, `/dashboard`
      and the three profile routes
- [ ] **No views** — no `resources/views/categories/`, `items/` or `transactions/`
- [ ] **No form request classes** for validating inventory input
- [ ] **Category CRUD** — index, create, edit, delete
- [ ] **Item CRUD** — index, create, edit, delete
- [ ] **Stock in / stock out / adjustment flow** — the core purpose of the app
- [ ] **Keeping `items.current_stock` in sync with transactions** — the column is stored
      rather than derived, so a deliberate strategy is needed (observer, service class, or
      DB transaction on every movement). Worth deciding before writing the flow, since
      getting it wrong causes drift between the two sources of truth.
- [ ] **Low-stock detection** — `minimum_stock` exists but nothing compares against it
- [ ] **Transaction history** — per-item and global movement log
- [ ] **Dashboard** — still Breeze's "You're logged in!" placeholder
      ([dashboard.blade.php](resources/views/dashboard.blade.php)); no counts, no low-stock
      alerts, no recent activity
- [ ] **Navigation links** — [navigation.blade.php](resources/views/layouts/navigation.blade.php)
      links to Dashboard only; no Items / Categories / Transactions entries

### Roles & authorization — schema only
- [ ] The `role` column is stored but **nothing reads it** — no `isAdmin()` helper, no
      role middleware, no gates, no policies, no role-aware UI
- [ ] Admin and staff currently have identical access to everything

### Not yet started (confirm whether in scope)
- [ ] Search, filtering, sorting, pagination on listings
- [ ] Reports, CSV / Excel / PDF export
- [ ] Item images or attachments
- [ ] Barcode / QR scanning (the `sku` column is noted as barcode-capable)
- [ ] Suppliers, purchase orders, locations / warehouses
- [ ] Soft deletes on any model

### Testing & quality
- [ ] No tests for any inventory functionality
- [ ] `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` are still placeholders
- [ ] Test suite has not been run since the new migrations landed
- [ ] No `ItemSeeder` — the `items` and `inventory_transactions` tables are **empty (0 rows)**,
      so there is no realistic data to build listings against

### Minor cleanups
- [ ] Both seeders use `Model::insert()`, which bypasses Eloquent — seeded rows have
      **NULL `created_at` / `updated_at`**. Switching to `create()` or factories fixes it.
- [ ] Migration filenames omit the conventional `create_` prefix
      (`2026_09_08_084339_categories_table.php` rather than `..._create_categories_table.php`)
      — cosmetic only, migrations run fine
- [ ] [README.md](README.md) is still the stock Laravel readme — no project description
      or setup steps
- [ ] `@tailwindcss/vite` v4 is in `package.json` but unused (project uses the Tailwind 3
      PostCSS setup) — harmless, can be removed
- [ ] Trailing whitespace on a few migration lines — `vendor/bin/pint` will tidy it

---

## 4. Suggested next steps, in order

1. Add `$fillable` (or `#[Fillable]`), `casts()` and `HasFactory` to the three inventory
   models — nothing else can be built until this is done.
2. Add `role` to `User`'s `#[Fillable]` list, plus an `isAdmin()` helper.
3. Create factories and an `ItemSeeder` so there is data to develop listings against.
4. Build **Category CRUD** end to end — smallest slice, and it unblocks Items.
5. Build **Item CRUD**, showing category, stock level and a low-stock indicator.
6. Decide the `current_stock` sync strategy, then build the **stock in / out / adjustment**
   flow inside a DB transaction.
7. Add the transaction history listing.
8. Replace the placeholder dashboard with real figures; add the navigation links.
9. Introduce role middleware / policies once the screens exist.
10. Add feature tests as each slice lands.

---

## 5. Handy commands

```bash
composer setup            # install deps, generate key, migrate, build assets
composer dev              # serve + queue worker + vite, all at once
composer test             # run the test suite
php artisan migrate
php artisan migrate:fresh --seed   # rebuild schema and reseed
php artisan migrate:status
vendor/bin/pint           # format code
```

## 6. Seeded login credentials

| Email            | Password   | Role  |
| ---------------- | ---------- | ----- |
| `admin@test.com` | `admin123` | admin |
| `user@test.com`  | `user123`  | staff |
