# MyInventory v2 - Project Status

- **Last reviewed:** 14 September 2026
- **Repository:** `C:\laragon\www\MyInventoryv2\myinventoryv2`
- **Branch / HEAD:** `main` at `94a7742` (matches `origin/main`)
- **Local URL:** `http://myinventoryv2.test`
- **Stack:** Laravel 13.30.1, PHP 8.4.12, MySQL, Blade, Alpine.js 3.17, Tailwind CSS 3.4, Vite 8.2
- **Local mail:** SMTP via Mailpit (`127.0.0.1:1025`; UI at `http://localhost:8025`)

This is a small in-house cafe inventory project and a learning project for a junior developer. Prefer clear Laravel conventions, explain architectural and security decisions, and avoid enterprise-scale abstractions unless the business scope requires them.

---

## 1. Current state

The application has a working Laravel foundation, Breeze authentication, category and item management, stock movements, and basic role-based user management. The database records stock transactions and keeps each item's current stock synchronized inside a locked database transaction.

The project is not production-ready yet. Dashboard/reporting work has not started, inventory features have no dedicated automated coverage, and the user-management implementation has several authorization and access-lifecycle issues listed in Section 4.

**Rough completion:** about 65-70%. The core workflow exists, but security hardening, inventory tests, dashboard/history screens, and release cleanup remain.

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

## 4. Known issues and risks

### High priority: user onboarding and authorization

- [ ] **Superadmin immutability can be bypassed with a direct request.** `UserPolicy::before()` returns `true` for every superadmin ability before `delete()` or `updateRole()` can reject a superadmin target. A superadmin can therefore send a direct route request to demote or delete the superadmin account even though the user is hidden from the listing.
- [ ] **Profile self-deletion bypasses `UserPolicy`.** Breeze's profile delete action allows any authenticated account, including the superadmin, to delete itself after password confirmation.
- [ ] **"Exactly one superadmin" is an application convention, not a database invariant.** The seeder creates one, but the schema does not enforce cardinality.
- [ ] **Public self-registration is still enabled.** `/register` creates ordinary users outside the invite flow. Confirm whether this is intended for an internal cafe application; disable it if onboarding must be invite-only.
- [ ] **Email verification is scaffolded but not enforced.** `App\Models\User` does not implement Laravel's `MustVerifyEmail` contract, so the `verified` middleware does not block unverified users and registration does not send the framework verification notification.

### High priority: data and migration safety

- [ ] **Deleting a user cascades to their inventory transactions.** The `inventory_transactions.user_id` foreign key uses `cascadeOnDelete()`, so deleting a staff account also removes their stock-movement history. Consider retaining users, soft-deleting them, or using a nullable/restricted foreign key before transaction history becomes an audit requirement.
- [ ] **Migration history was rewritten after the earlier migration was committed.** Commit `8ed2048` moved `superadmin` into the original users migration and removed `2026_09_11_034008_add_superadmin_to_users_role_enum.php`. Fresh databases and the current local schema work, but environments that already ran the earlier committed migration can have different migration history. Confirm the strategy before pushing or deploying the two local commits.

### Functional gaps

- [ ] Dashboard remains Breeze's `You're logged in!` placeholder.
- [ ] No dashboard totals, low-stock alert panel, or recent activity.
- [ ] Low stock is only indicated on the item listing; there is no consolidated alert workflow.
- [ ] No global or per-item transaction history screen, although transactions are recorded.
- [ ] No search or filtering. Listings have fixed name sorting and basic pagination, but no user-selectable sorting.
- [ ] No reports or CSV export.
- [ ] No item images, barcode/QR scanning, suppliers, purchase orders, or multiple locations/warehouses.
- [ ] No soft deletes.

### Cleanup and maintainability

- [ ] `CategorySeeder` uses `insert()`, so timestamps are not populated and Eloquent model behavior is bypassed.
- [ ] `UserController` contains stale imports and inconsistent formatting; several controllers/models also lack explicit return types.
- [ ] Some Malay comments remain in older files.
- [ ] Resource routes register unused `show` endpoints for Category and Item.
- [ ] `README.md` is still the stock Laravel readme.
- [ ] `APP_NAME` is still `Laravel` instead of the product name.
- [ ] `@tailwindcss/vite` 4.3 is installed but unused because the project builds Tailwind 3 through PostCSS.
- [ ] No `ItemSeeder`; the existing `UserFactory` has no named role states.
- [ ] No rate limit or duplicate-submission protection on user creation/invite emails.

---

## 5. Recommended next steps

1. Correct superadmin authorization, protect profile deletion, and add a policy permission matrix.
2. Decide whether public registration should remain enabled.
3. Confirm the migration-history strategy before deploying.
4. Decide how user deletion should preserve inventory transaction history.
5. Add focused Category, Item, and Stock Movement feature tests.
6. Build a useful dashboard with item count, low-stock count, and recent transactions.
7. Add global and per-item transaction history views.
8. Complete the cleanup items before portfolio or production use.

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

- Local `main` matches `origin/main` at `94a7742` (`chore: configure Laravel Boost and project mentoring rules`).
- Expected uncommitted changes from the current milestone are `UserController.php`, `UserTest.php`, and this status document.

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
