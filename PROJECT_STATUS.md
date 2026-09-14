# MyInventory v2 - Project Status

- **Last reviewed:** 14 September 2026
- **Repository:** `C:\laragon\www\MyInventoryv2\myinventoryv2`
- **Branch / HEAD:** `main` at `f3b5d94` (matches `origin/main`)
- **Local URL:** `http://myinventoryv2.test`
- **Stack:** Laravel 13.30.1, PHP 8.4.12, MySQL, Blade, Alpine.js 3.17, Tailwind CSS 3.4, Vite 8.2
- **Local mail:** SMTP via Mailpit (`127.0.0.1:1025`; UI at `http://localhost:8025`)

This is an entry-level Laravel portfolio and learning project for a fresh graduate. Its purpose is to demonstrate Laravel fundamentals to potential employers, not to serve as a production system or a product for clients. Prefer clear framework conventions, readable code, and a small feature set that can be explained confidently in an interview.

## Project objective

Build a simple cafe inventory application that demonstrates:

- Laravel authentication and basic role-based authorization;
- CRUD operations for users, categories, and items;
- stock-in, stock-out, and stock-adjustment workflows;
- Eloquent relationships, validation, database transactions, and pagination;
- a useful dashboard and inventory transaction history;
- focused PHPUnit feature tests and clear Git commits;
- a portfolio README that explains the project and how to run it.

Advanced business features such as purchase orders, suppliers, barcode scanning, multiple warehouses, enterprise audit systems, and production deployment infrastructure are outside the current portfolio scope.

---

## 1. Current state

The application has a working Laravel foundation, Breeze authentication, category and item management, stock movements, and basic role-based user management. The database records stock transactions and keeps each item's current stock synchronized inside a locked database transaction.

The main inventory workflow is working. The project still needs a simple dashboard, transaction-history screens, focused inventory tests, a few clear authorization fixes, and portfolio documentation.

**Rough completion for the portfolio scope:** about 70-75%.

### Verification snapshot

- `php artisan test --compact` on 14 September: **34 tests passed, 99 assertions**.
- `npm run build` on 13 September: **passed** with Vite 8.2.2.
- `php artisan route:list --except-vendor`: **41 application routes**.
- All six migration files currently present report as run in the local MySQL database.
- The local `users.role` column is `enum('superadmin', 'admin', 'staff')`.
- Runtime drivers: MySQL database, database cache/queue/session, and SMTP mail.

Passing tests do not mean all important behavior is covered. The known gaps below are outside the current suite.

---

## 2. Implemented features

### Foundation and authentication

- [x] Laravel 13 application running locally under Laragon.
- [x] MySQL database configured.
- [x] Mailpit configured for local email testing.
- [x] Blade, Alpine.js, Tailwind CSS 3, and Vite frontend toolchain.
- [x] Laravel Breeze 2.4 authentication: registration, login, logout, password reset, password confirmation, profile management, and email-verification scaffolding.
- [x] Base controller includes Laravel's `AuthorizesRequests` trait.

### Database and seed data

- [x] `users`, `password_reset_tokens`, `sessions`, `categories`, `items`, and `inventory_transactions` schemas.
- [x] Three user role values: `superadmin`, `admin`, and `staff`.
- [x] `UserSeeder` creates one superadmin (`Ahmad`) and one staff user (`Shahrul`) through Eloquent.
- [x] `CategorySeeder` creates Packaging and Coffee and Beverages categories.
- [x] Models define relationships, mass-assignable fields, casts, and factories where currently available.

### Category management

- [x] Admin-only category listing, create, edit, update, and delete screens.
- [x] Unique category-code validation.
- [x] Category list is sorted by name and paginated at 10 rows.
- [x] Application-level guard prevents deleting a category that still contains items.
- [ ] The generated `categories.show` route exists, but its controller action is still a stub and there is no show view.

### Item and stock management

- [x] Authenticated users can list, create, edit, update, and delete items.
- [x] Item list is sorted by name and paginated at 15 rows.
- [x] Item list displays a low-stock badge when `current_stock <= minimum_stock`.
- [x] Initial stock is recorded as an inventory transaction rather than directly assigned.
- [x] Stock in, stock out, and signed adjustment forms.
- [x] `StockMovementService` uses a database transaction and `lockForUpdate()` to synchronize `items.current_stock` and transaction records.
- [x] Negative resulting stock is rejected through `InsufficientStockException`.
- [x] Items with recorded transactions cannot be deleted through the item controller.
- [ ] The generated `items.show` route exists, but its controller action is still a stub and there is no show view.

### User management

- [x] Admin middleware protects category and user-management routes.
- [x] `UserPolicy` and Form Request authorization are wired into user-management actions.
- [x] Admins can list non-superadmin users, create staff/admin users, promote staff, demote other admins, and delete staff.
- [x] Regular admins are blocked from deleting themselves, deleting another admin, changing their own role, or modifying/deleting a superadmin.
- [x] User listing is sorted by name and paginated at 10 rows.
- [x] New users receive `WelcomeNewStaffNotification` with a token for Breeze's existing password-reset flow.
- [x] Invited users receive a random, unknown placeholder password and choose their real password through the reset-token flow.
- [x] The create and role-update requests only accept `admin` or `staff`; `superadmin` is not offered by the UI.
- [x] Navigation only displays the Users link to admin-level users.
- [x] The user index intentionally excludes superadmin accounts from its query.

The intended rule is that one fixed superadmin exists and cannot be changed through the UI. That intent is **not fully enforced server-side yet**; see Section 4.

---

## 3. Automated test coverage

### Covered and passing

- [x] Breeze authentication, password, verification, and profile tests.
- [x] Basic home-page and unit placeholders.
- [x] Nine user-management feature tests cover:
  - staff cannot open user management;
  - admin cannot delete self, another admin, or a superadmin;
  - admin can delete staff;
  - admin can promote staff;
  - admin cannot change their own role or a superadmin's role;
  - the complete create -> notification -> set password -> login flow, including protection against an empty initial password.

### Missing or incomplete coverage

- [ ] No dedicated Category feature tests.
- [ ] No dedicated Item feature tests.
- [ ] No `StockMovementService` or stock-movement endpoint tests, including insufficient stock and adjustment cases.
- [ ] No tests for invalid user-creation or role-update payloads.
- [ ] No complete policy matrix, especially superadmin acting on self or another superadmin.
- [ ] `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` remain placeholders.

---

## 4. Remaining portfolio work

### Required for a clear portfolio demonstration

- [ ] Fix the direct-request bug that allows the superadmin account to be deleted or demoted.
- [ ] Prevent the superadmin from deleting itself through the profile page.
- [ ] Prevent deletion of a user who owns inventory transactions so the transaction history is retained. A simple deletion guard is sufficient for this project; account deactivation and soft deletes are not required.
- [ ] Add focused Category, Item, and Stock Movement feature tests.
- [ ] Replace the placeholder dashboard with item count, low-stock count, and recent transactions.
- [ ] Add global and per-item transaction-history pages.
- [ ] Replace the default Laravel README and application name with portfolio-specific information.

### Simple decisions for this portfolio

- Public registration can remain enabled because it demonstrates Laravel Breeze and allows a reviewer to create a staff account. Registered users receive the default `staff` role.
- The current migration history is acceptable while the project is local and has no shared or production database. Confirm that a disposable database can be built from the current migrations and seeders. For any future shared deployment, use new migrations instead of editing migrations that have already run.
- Email verification may remain as scaffolding and should be described honestly as not enforced.

### Optional future learning

- Enforce exactly one superadmin at the database or service layer.
- Add user deactivation or soft deletes.
- Add search, filtering, CSV export, item images, or barcode scanning.
- Add suppliers, purchase orders, multiple locations, or advanced reporting.
- Add production deployment, rate limiting, and more extensive audit controls.

---

## 5. Recommended next steps

Complete one small milestone at a time:

1. Fix the two superadmin authorization bugs and add a few focused tests for those exact cases.
2. Add a simple guard that stops a user with inventory transactions from being deleted, with one test.
3. Add focused tests for the main Category, Item, and Stock Movement workflows. Cover the successful action and the most important failure for each feature.
4. Build the dashboard with three parts: total items, low-stock items, and five recent transactions.
5. Build a paginated transaction list, then reuse the same idea for an individual item's history.
6. Prepare the portfolio presentation: update the application name and README, explain the features and setup steps, and include a few screenshots.
7. Run the full test suite and frontend build, then verify the application from login through a complete stock movement.

---

## 6. Current design decisions

- Stock changes go through `StockMovementService`; item editing does not directly change `current_stock`.
- Inventory updates and transaction creation are atomic and lock the item row to prevent concurrent lost updates.
- `TransactionType` is a PHP enum with `In`, `Out`, and `Adjustment` cases.
- `isAdmin()` means admin-level access or higher and therefore includes superadmin; `isSuperAdmin()` and `isStaff()` are strict checks.
- User-management authorization is intended to live in `UserPolicy` and Form Requests, with Blade checks used only for presentation.
- User invitations reuse Laravel's password-reset token infrastructure and a custom welcome notification.
- Superadmin is omitted from the user-management listing and cannot be selected as a role in normal forms.

---

## 7. Repository state

- Local `main` matches `origin/main` at `f3b5d94` (`feat(controller): update user controller`).
- This status document contains the uncommitted portfolio-scope revision.

---

## 8. Useful commands

```bash
composer setup
composer dev
composer test
php artisan test --compact
php artisan route:list --except-vendor
php artisan migrate:status
npm run build
vendor/bin/pint --format agent
```

`migrate:fresh` and `migrate:refresh` drop data. Do not run either against a database with data that must be kept.

---

## 9. Seeded local credentials

| Email | Password | Role |
| --- | --- | --- |
| `admin@test.com` | `admin123` | superadmin |
| `user@test.com` | `user123` | staff |

No admin-role account is seeded by default.
