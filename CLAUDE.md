# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# First-time setup
composer setup

# Start dev environment (runs server, queue, log tail, and Vite concurrently)
composer dev

# Run all tests
composer test

# Run a single test
php artisan test --filter=TestName

# Code style (Laravel Pint)
./vendor/bin/pint

# Run migrations
php artisan migrate

# Seed permissions and default roles
php artisan db:seed --class=PermissionSeeder

# Build frontend assets
npm run build
```

## Architecture

### Multi-tenant (Multi-company) Model

This is a multi-company project management SaaS. Every user can belong to multiple companies. The currently active company is stored in `users.active_company_id`.

**Auth flow**: Login → Company Select (`/company/select`) → Dashboard. The `company.selected` middleware (`EnsureCompanySelected`) blocks access to all main routes if no company is active.

**Data isolation**: Every entity (customers, projects, quotations, invoices, finance entries, etc.) has a `company_id` column. Controllers must always scope queries to the active company — never fetch data without a `WHERE company_id = ?` clause.

### ScopedToCompany Trait

All resource controllers use `App\Http\Controllers\Concerns\ScopedToCompany`, which provides:
- `$this->company()` — returns the active `Company` model
- `$this->companyId()` — returns the integer company ID

Controllers then call `abort_if($model->company_id !== $this->companyId(), 403)` to guard ownership.

### Two-layer Authorization

1. **Super Admin** (`users.is_super_admin = true`): System-wide flag. Can manage all companies, impersonate any user, and access `/admin/*` routes. Guarded by `super.admin` middleware.

2. **Spatie Roles & Permissions**: Per-company roles (Admin, Manager, Staff, Viewer) with permissions in the format `module.action` (e.g., `customers.view`, `projects.edit`). Seeded by `PermissionSeeder`. Check permissions in Blade with `@can('projects.create')` and in controllers with `$this->authorize()` or `abort_unless(auth()->user()->can(...))`.

### Impersonation

Super admins can impersonate any user. The impersonating admin's ID is stored in both `session('impersonating_admin_id')` and `users.impersonating_id`. `User::isImpersonating()` checks the latter.

### Custom Fields

A per-company, per-module extension system. `CustomField` records define fields for a specific module (e.g., `projects`, `customers`). Values are stored in `CustomFieldValue` as a polymorphic relation (`record_type` + `record_id`). Field values are stored as strings; array types (e.g., multi-select) are JSON-encoded. When adding custom field support to a new module, follow the pattern in `ProjectController::saveCustomFields()`.

### Global View Data

`AppServiceProvider::boot()` shares `$activeCompany`, `$primaryColor`, and `$secondaryColor` to every Blade view via `View::composer('*', ...)`. These are always available in templates without being explicitly passed from controllers.

### Key Domain Models

- **Quotation** → can be revised (creates new version) or converted to a **Project** (`quotations.convert`)
- **Invoice** → has line items and payments; `Invoice::recalculate()` re-derives subtotal/tax/discount/total and calls `updateStatus()` to set status to `sent`/`partial`/`paid`/`cancelled` automatically
- **Project** → has tasks, finance entries (credit/debit), uploaded files, and custom field values; `SoftDeletes` enabled
- **FinanceEntry** → `type` is either `credit` (received) or `debit` (expense); profit/loss is `total_credit - total_debit`
- **UserColumnPreference** — stores per-user, per-company, per-module column visibility settings

### Frontend Stack

- **Blade** templates with **Tailwind CSS 4** (via Vite plugin)
- **Alpine.js** for client-side interactivity
- **Livewire 4** available but used selectively
- **DomPDF** (`barryvdh/laravel-dompdf`) for invoice and quotation PDF export — templates live in `resources/views/pdf/`
- Fonts loaded via Bunny Fonts (Instrument Sans) in `vite.config.js`

### Database

Uses **SQLite** by default (`database/database.sqlite`). Tests run against an in-memory SQLite DB (`DB_DATABASE=:memory:`). The project supports switching to MySQL/PostgreSQL via `.env` without code changes.

### File Storage

Uses the `public` disk (`storage/app/public`, symlinked to `public/storage`). Project files are stored at `projects/{id}/files/`. Company logos and user avatars are stored directly under `storage/`. Run `php artisan storage:link` if the symlink is missing.
