# CLAUDE.md — MadrasaFunds Module

Guidance for Claude Code when working with the **MadrasaFunds** module of Lara Dashboard.

## Module Overview

**Namespace**: `Modules\MadrasaFunds\`
**Alias**: `madrasafunds`
**Path**: `modules/madrasafunds/`
**CSS prefix**: `mf`
**Route prefix**: `admin/madrasafunds` · route names `admin.madrasafunds.*`
**Table prefix**: `madrasa_`

A fund / donation management system for a Madrasa (Islamic school). Tracks multiple fund
types (general donation, construction, trust, student fees, food fees), departments,
students, and issues bilingual (English + Urdu RTL) printable receipts with reports.

It follows the same architecture as the `schoolmanagement` reference module.

## Domain Model

| Model | Table | Notes |
|-------|-------|-------|
| `Fund` | `madrasa_funds` | `type` cast to `FundType` enum (donation/fees/trust) |
| `Department` | `madrasa_departments` | bilingual name |
| `Student` | `madrasa_students` | belongs to a Department |
| `Receipt` | `madrasa_receipts` | auto `receipt_number` (RCP-YYYY-NNNN) via `booted()` |

`FundType` enum: `donation`, `fees`, `trust`. The receipt form shows donor fields for
donation/trust funds and a student autocomplete for `fees` funds (driven by JS).

## Quick Commands

```bash
php artisan module:migrate MadrasaFunds      # run migrations (creates tables + permissions)
php artisan module:seed MadrasaFunds         # seed the 5 predefined fund types (FundSeeder)
php artisan module:compile-css MadrasaFunds  # build prefixed Tailwind CSS to public/build-madrasafunds
php artisan test modules/madrasafunds/tests  # run feature tests
vendor/bin/pint modules/madrasafunds         # fix code style
```

First-time CSS build needs module deps: `cd modules/madrasafunds && npm install && npm run build`.

## Architecture

- **Thin controllers** extend `MadrasaFundsController`; they authorize → validate → delegate to a service → redirect.
- **Services**: `FundService`, `DepartmentService`, `StudentService` extend `App\Services\BaseService`
  (so `create`/`update`/`delete` take a `$data` array / `int $id`). `ReceiptService` and
  `ReportService` are richer plain classes.
- **FormRequests** for every write action (`Store*`/`Update*`, plus `CancelReceiptRequest`).
- **Datatables** are plain JS (`MfDatatable` in `resources/views/partials/datatable-script.blade.php`).
  Each list controller exposes a `data()` JSON endpoint registered *before* the resource route.
- **Views** extend `<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">` and use shared
  `x-inputs.*`, `x-buttons.submit-buttons`, `x-card` components.
- **Hooks**: `ReceiptActionHook` / `ReceiptFilterHook` enums fired through `App\Support\Facades\Hook`.

## Permissions

Group `madrasa_funds`, registered in `ModuleService::getPermissions()` and seeded by the
`2026_06_18_000005_seed_madrasa_funds_permissions` migration (assigned to Superadmin, Admin,
and a subset to accountant):

`view_any`, `create`, `edit`, `delete`, `print_receipt`, `view_reports`, `manage_funds`, `manage_departments`.

## Menu

Registered via `MenuService` (hooked on `AdminFilterHook::ADMIN_MENU_GROUPS_BEFORE_SORTING`).
Top-level "Madrasa Funds" group with children: Overview, New Receipt, Receipts, Students,
Reports, Funds, Departments.

## Bilingual Helper

Use `mf_bi('English')` (defined in `app/Helpers/bilingual.php`, map in `lang/ur.php`) to render
"English / اردو" labels. It is namespaced `mf_bi` (not the global `bi`) to avoid clashing with
other modules. Add new keys to `lang/ur.php`.

## Tailwind CSS Prefix

Every module-specific utility must be prefixed `mf:` (e.g. `mf:py-4 mf:flex mf:dark:text-white`).
Shared component classes (`btn`, `form-control`, `badge`, `card`) come from the core app — no prefix.
Do **not** use `@apply` in `resources/assets/css/app.css` (conflicts with `prefix()`).

## Conventions

- `declare(strict_types=1);` in every PHP class.
- Use `config()` not `env()` outside config files.
- Eager load relationships to avoid N+1.
- Named routes everywhere; never hardcode URLs in Blade.
- Migrations are upgrade-safe: literal strings, `Schema::hasTable()` guards, `DB::table()` for seeding.
- Dark mode + accessibility (`aria-*`, `aria-hidden` on decorative icons) in all views.
