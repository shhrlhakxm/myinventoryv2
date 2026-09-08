# MyInventory v2 — Project Status

**Last reviewed:** 8 September 2026
**Location:** `C:\laragon\www\MyInventoryv2\myinventoryv2`
**Stack:** Laravel 13.17 · PHP 8.4.12 · MySQL · Blade + Alpine.js + Tailwind CSS 3 · Vite 8
**Local URL:** `http://myinventoryv2.test` (Laragon)

---

## 1. Where the project stands

The project is at the **early scaffolding stage**. A fresh Laravel 13 app with Laravel Breeze
authentication is fully in place, and the inventory domain has been *started* — three Eloquent
models with their relationships are written — but nothing behind them exists yet: no migrations,
no controllers, no routes, no views, no tests.

In short: **you can register and log in, but you cannot yet manage any inventory.**

Rough completion: **~20%** (foundation done, features not started).

---

## 2. What has been done

### Application foundation
- [x] Laravel 13.17 project created and running under Laragon
- [x] `.env` configured — MySQL connection pointing at the `myinventory` database
- [x] `APP_KEY` generated; `APP_URL` set to `http://myinventoryv2.test`
- [x] Session, cache and queue drivers all set to `database`
- [x] Mail driver set to `log` (fine for local dev; needs a real mailer before production)
- [x] Composer helper scripts available — `composer setup`, `composer dev`, `composer test`
- [x] Frontend toolchain wired up: Vite 8 + Tailwind 3 + Alpine.js, and assets are compiled
      (`public/build/` exists)
- [x] Code style tooling installed — Laravel Pint

### Authentication & user account (Laravel Breeze 2.4, Blade stack)
- [x] Register, login, logout
- [x] Forgot password / password reset flow
- [x] Password confirmation screen
- [x] Email verification scaffolding (routes, controllers, views)
- [x] Profile page — update name/email, change password, delete account
- [x] Full UI component library — buttons, inputs, labels, modal, dropdown, nav links
- [x] App and guest layouts plus the navigation bar
- [x] Breeze's own feature tests all present and passing-ready
      (`tests/Feature/Auth/*`, `tests/Feature/ProfileTest.php`)

### Inventory domain — models only
- [x] [Category.php](app/Models/Category.php) — `hasMany(Item)`
- [x] [Item.php](app/Models/Item.php) — `belongsTo(Category)`, `hasMany(InventoryTransaction)`
- [x] [InventoryTransaction.php](app/Models/InventoryTransaction.php) — `belongsTo(User)`,
      `belongsTo(Item)`
- [x] [User.php](app/Models/User.php#L33) — `hasMany(InventoryTransaction)` relationship added

### Database (framework tables only)
- [x] `users`, `password_reset_tokens`, `sessions` migration
- [x] `cache`, `cache_locks` migration
- [x] `jobs`, `job_batches`, `failed_jobs` migration
- [x] `UserFactory` and a `DatabaseSeeder` that creates one test user
      (`test@example.com`)

---

## 3. What has NOT been done

### Blocking — nothing else can work until these exist
- [ ] **Migration for `categories`** — no table, so `Category` cannot be saved or read
- [ ] **Migration for `items`** — no table; needs at minimum name, SKU, description,
      `category_id`, quantity, unit, cost/price, reorder level, timestamps
- [ ] **Migration for `inventory_transactions`** — no table; needs `item_id`, `user_id`,
      type (stock in / stock out / adjustment), quantity, note, timestamps
- [ ] **Foreign keys and indexes** on the above
- [ ] Run `php artisan migrate` — the `myinventory` database has never been migrated
      (verified: `migrate:status` currently fails, MySQL was not running at review time)

### Models — incomplete
- [ ] No `$fillable` / `#[Fillable]` attributes on `Category`, `Item` or
      `InventoryTransaction`, so mass assignment will be rejected
- [ ] No `casts()` — quantities, prices and dates are untyped
- [ ] No `HasFactory` trait on the three inventory models
- [ ] No factories: `CategoryFactory`, `ItemFactory`, `InventoryTransactionFactory`
- [ ] No seeders for demo categories/items
- [ ] No computed stock helper (e.g. current quantity derived from transactions), no
      low-stock scope, no soft deletes

### Features — not started
- [ ] **Category CRUD** — controller, routes, form requests, index/create/edit views
- [ ] **Item CRUD** — controller, routes, form requests, index/create/edit views
- [ ] **Stock in / stock out** — the transaction recording flow, which is the core of the app
- [ ] **Stock level tracking** — keeping item quantity in sync with transactions
- [ ] **Transaction history** — per-item and global movement log
- [ ] **Dashboard** — still Breeze's placeholder "You're logged in!"
      ([dashboard.blade.php](resources/views/dashboard.blade.php)); no stats, no low-stock
      warnings, no recent activity
- [ ] **Navigation links** — [navigation.blade.php](resources/views/layouts/navigation.blade.php)
      only links to Dashboard; no Items / Categories / Transactions entries
- [ ] Search, filtering, sorting and pagination on any listing
- [ ] Reports or CSV / Excel / PDF export
- [ ] Item images or file attachments
- [ ] Barcode / QR code support
- [ ] Suppliers, purchase orders, locations or warehouses (if these are in scope)
- [ ] Roles and permissions — every logged-in user currently has identical access
- [ ] Authorization policies for any model

### Routing
- [ ] [routes/web.php](routes/web.php) contains only `/`, `/dashboard` and the profile
      routes — no inventory routes at all

### Testing & quality
- [ ] No tests for any inventory functionality
- [ ] `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` are still the
      untouched placeholders
- [ ] Test suite has not been run against a migrated database

### Project hygiene
- [ ] **Not under version control** — this directory is not a git repository, so there is no
      history and no way to undo. Worth fixing early.
- [ ] [README.md](README.md) is still the stock Laravel readme — no project description or
      setup instructions
- [ ] `@tailwindcss/vite` v4 is in `package.json` but unused (the project uses the Tailwind 3
      PostCSS setup) — harmless, but can be removed

---

## 4. Suggested next steps, in order

1. **`git init`** and make a first commit, before the codebase grows further.
2. Start MySQL in Laragon and create the `myinventory` database.
3. Write the three inventory migrations, then `php artisan migrate`.
4. Fill in `$fillable`, `casts()` and `HasFactory` on the three models; add factories.
5. Build Category CRUD end to end — it is the smallest slice and unblocks Items.
6. Build Item CRUD.
7. Build the stock in / stock out transaction flow (the actual point of the app).
8. Replace the placeholder dashboard with real figures and add the navigation links.
9. Add feature tests as each slice lands.

---

## 5. Handy commands

```bash
composer setup     # install deps, generate key, migrate, build assets
composer dev       # serve + queue worker + vite, all at once
composer test      # run the test suite
php artisan migrate
vendor/bin/pint    # format code
```
