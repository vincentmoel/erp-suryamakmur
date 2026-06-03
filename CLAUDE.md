# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run development server
php artisan serve

# Run migrations
php artisan migrate

# Clear compiled views (always run after editing Blade components)
php artisan view:clear

# Clear config cache
php artisan config:clear

# Laravel Pint (code style)
./vendor/bin/pint

# Vite (frontend assets)
npm run dev
npm run build
```

## Architecture Overview

Laravel 10 ERP application. **This is a production app.**

- **Never** run destructive migrations (drop column, drop table, change column type) without explicit instruction.
- **Never** use `php artisan migrate:fresh` or `migrate:reset`.
- **Never** seed or truncate production data.
- Always write migrations that are safely additive (add column nullable, add table, etc.).

### N+1 Queries — Strictly Forbidden

This app serves real users. N+1 queries are not acceptable.

- `BaseModel` already eager-loads `user_created_by` and `user_updated_by` via `$with`. Do not add more relations to `$with` unless they are needed on **every** query for that model.
- In `DataTable::query()`, always eager-load any relation that will be accessed in `dataTable()` columns:
  ```php
  public function query(): QueryBuilder
  {
      return $this->model::with('relation1', 'relation2')->latest()->newQuery();
  }
  ```
- In controllers, use `with()` / `load()` before passing data to views when relations are needed.
- In Blade views, never call a relation method inside a loop without it being eager-loaded first.

### The CRUD Pattern

Every module follows a strict, identical pattern. Before creating a new module, study `Category` (simplest) or `Customer` (with enum + conditional fields) as references.

**Files to create for each new module:**

| File | Convention |
|------|-----------|
| `database/migrations/YYYY_MM_DD_XXXXXX_create_{plural}_table.php` | Always include `created_by`, `updated_by` FK to users, `timestamps()`, `softDeletes()` |
| `app/Models/{Name}.php` | Extend `BaseModel`, use `SoftDeletes` |
| `app/DataTables/{Name}DataTable.php` | Extend `BaseDataTable` |
| `app/Http/Requests/{Name}Request.php` | Split `store()` and `update()` methods |
| `app/Http/Controllers/{Name}Controller.php` | Extend `BaseController` |
| `resources/views/{plural}/index.blade.php` | |
| `resources/views/{plural}/create.blade.php` | |
| `resources/views/{plural}/edit.blade.php` | |
| `resources/views/{plural}/show.blade.php` | |

**Then register in:**
- `app/Enums/Module.php` — add `case Name = "Name";`
- `routes/web.php` — add route group with permission middleware
- `config/sidebar.php` — add sidebar entry

### BaseController

`app/Http/Controllers/BaseController.php` handles all CRUD actions automatically: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `trashed`, `restore`.

Controller constructor signature:
```php
parent::__construct(
    ModelClass::class,  // model
    'route-prefix',     // view folder & route prefix (plural, kebab)
    'Title',            // human-readable singular name
    'route-prefix',     // route name prefix
    Module::Name->name, // module permission key
    NameRequest::class, // form request
    NameDataTable::class,
);
```

Override `create()` and `edit()` only when extra data must be passed to the view (e.g., enum options for selects).

### BaseModel

`app/Models/BaseModel.php` — all models extend this. It automatically:
- Sets `created_by` / `updated_by` on create/update via `CreatedByUpdatedBy` trait
- Eager-loads `user_created_by` and `user_updated_by` relations
- Uses `$guarded = ['id', 'created_at', 'updated_at', 'deleted_at']`

### BaseDataTable

`app/DataTables/BaseDataTable.php` — handles action buttons, soft delete trashed view, and common column formatters (`created_at`, `updated_at`, `created_by`, `updated_by`).

Override `dataTable()` only when extra column formatting is needed (e.g., enum label rendering):
```php
public function dataTable(QueryBuilder $query): EloquentDataTable
{
    return parent::dataTable($query)
        ->editColumn('type', fn($row) => $row->type->label());
}
```

### Enums

All enums live in `app/Enums/`. They use `EnumToArray` and `EnumLabel` traits. Each enum must implement a `label(): string` method.

To use an enum in a form, pass `$enumName::cases()` from the controller and render with `<x-form.single-select>`.

### Permissions & Middleware

Permissions are stored in session (`Session::get('permissions')[$module][$action]`). Routes use:
```php
->middleware("check.permission:ModuleName,read|create|update|delete|restore")
```

Module names come from `Module` enum values. Always add `trashed` and `restore` routes at the **top** of the route group (before wildcard `{encryptedId}` routes).

### ID Encryption

All IDs in URLs are encrypted using `App\Helpers\Encryption`. Never expose raw IDs in URLs. Route parameter is always named `{encryptedId}`. Decrypt with `Encryption::decrypt($encryptedId)`.

### Form Components

| Component | Usage |
|-----------|-------|
| `<x-form.field name="" label="" :required="true">` | Wraps any input with label + error display |
| `<x-form.single-select name="" :options="[]" :selected="$val" placeholder="">` | Searchable single-select (combobox). Options format: `[['value' => '', 'label' => '']]` |
| `<x-form.multi-select name="" :options="[]" :selected="[]" placeholder="">` | Searchable multi-select. Submits as `name[]` |
| `<x-form.file-upload name="" :max-size-mb="2">` | File upload with preview |

**Textarea:** always add `style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"` because the `.input` CSS class forces a fixed height.

**Grid layout:** group form fields using `<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">` — do not stack all fields flat.

### File / Image Handling

Use `App\Helpers\FileManager::store()` to save uploaded files:

```php
// Store to storage/app/public/{folder}/
$path = FileManager::store($request->file('photo'), 'customers');
// Returns e.g. "customers/hashed-filename.jpg"
// Access via: asset('storage/' . $path)
```

The helper hashes the filename and stores to the `public` disk by default. Pass a third argument to use a different disk.

### Icons

Icons are registered in `resources/views/components/icon.blade.php` as a flat PHP array (`$icons`) mapping name → SVG inner path string. Usage: `<x-icon name="icon-name" class="size-4" />`.

**Before using any icon name, read the array in that file to confirm it exists.** If it doesn't exist, add the SVG `<path>` string to the array under the appropriate section comment (USER & AUTH, NAVIGATION, ACTIONS, FINANCE & ERP, MISC). Use Heroicons outline style (24×24 viewBox, stroke-based) to stay consistent with the rest of the icons.

### Views Structure

- `resources/views/layouts/main.blade.php` — main layout, extended by all pages
- `resources/views/partials/form-actions-create.blade.php` — Save + Save & Add Another buttons
- `resources/views/partials/form-actions-edit.blade.php` — Update button
- `resources/views/components/` — Blade components (`x-icon`, `x-form.field`, `x-datatable`, etc.)

### Sidebar

Configured in `config/sidebar.php` as a nested PHP array. Supports flat items, groups, and dropdown menus. Always use route names (not URLs) for internal pages.

### DataTable (index view)

Index views use Yajra DataTables via AJAX. The `ajaxUrl` points to the same `index` route — the controller serves JSON when the DataTable requests it. Columns must match the model attributes or the `editColumn`/`addColumn` names defined in the DataTable class.
