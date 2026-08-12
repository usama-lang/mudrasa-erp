# CLAUDE.md — SchoolManagement Module

This file provides guidance to Claude Code (claude.ai/code) when working with the **SchoolManagement** module of Lara Dashboard.

## Module Overview

**Namespace**: `Modules\SchoolManagement\`
**Alias**: `schoolmanagement`
**Path**: `modules/SchoolManagement/`

> Update this section with a brief description of what this module does.

## Quick Commands

### Development (run from project root)

```bash
composer run dev          # Start dev server (Vite + PHP)
```

### Testing

```bash
# Run all module tests
php artisan test modules/SchoolManagement/tests/

# Run a specific test file
php artisan test modules/SchoolManagement/tests/Feature/ExampleTest.php

# Filter by test name
php artisan test --filter=SchoolManagement

# Fix code style
vendor/bin/pint --dirty
```

### Module Management

```bash
php artisan module:enable SchoolManagement    # Enable the module
php artisan module:disable SchoolManagement   # Disable the module
php artisan module:migrate SchoolManagement   # Run module migrations
php artisan module:seed SchoolManagement      # Seed module data
```

---

## Generating CRUD in 60 Seconds

Use the `module:make-crud` command to scaffold a full CRUD resource —
Model, Controller, Service, FormRequest, Datatable, Views, Permissions, Policy, and Routes — in one step.

### Option A — Define fields inline

```bash
php artisan module:make-crud SchoolManagement \
  --model=Post \
  --fields="title:string,body:text,status:select:Draft|Published|Archived,is_featured:toggle,thumbnail:media"
```

### Option B — Parse an existing migration

```bash
# 1. Create the migration first
php artisan make:migration create_schoolmanagement_posts_table \
  --path=modules/SchoolManagement/database/migrations

# 2. Edit the migration with your columns

# 3. Generate CRUD from that migration
php artisan module:make-crud SchoolManagement --migration=create_schoolmanagement_posts_table
```

### Supported Field Types

| Type | UI Control | DB Column |
|------|-----------|-----------|
| `string` | Text input | VARCHAR |
| `text` | Textarea | TEXT |
| `editor` | Rich text (TinyMCE) | TEXT |
| `integer` | Number input | INTEGER |
| `boolean` | Checkbox | BOOLEAN |
| `toggle` | Toggle switch | BOOLEAN |
| `select:A\|B\|C` | Dropdown | VARCHAR |
| `date` | Date picker | DATE |
| `datetime` | Datetime-local picker | DATETIME |
| `media` | Media library selector | FK → media |
| `file` | File upload | VARCHAR (path) |

### After Generating CRUD

1. Run migrations: `php artisan migrate`
2. Review `app/Models/YourModel.php` — check `$fillable` and `casts()`
3. Update `Livewire/Components/YourModelDatatable.php` headers/columns
4. Customize `resources/views/pages/{plural}/partials/form.blade.php`
5. Clear caches: `php artisan optimize:clear`
6. Access at: `/admin/schoolmanagement/{plural}`

---

## Module Architecture

### Directory Structure

```
modules/SchoolManagement/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SchoolManagementController.php     ← Base controller (extends app Controller)
│   │   │   └── PostController.php               ← Resource controller (thin, delegates to service)
│   │   └── Requests/
│   │       └── PostRequest.php                  ← FormRequest for validation
│   ├── Livewire/
│   │   └── Components/
│   │       └── PostDatatable.php                ← Datatable component (listing only)
│   ├── Models/
│   │   └── Post.php                             ← Eloquent model
│   ├── Policies/
│   │   └── PostPolicy.php                       ← Authorization policy
│   ├── Providers/
│   │   ├── SchoolManagementServiceProvider.php     ← Main provider (policies, gates)
│   │   ├── EventServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   └── LivewireServiceProvider.php
│   └── Services/
│       ├── MenuService.php                      ← Module menu registration
│       ├── ModuleService.php                    ← Bootstrap (permissions, settings)
│       └── PostService.php                      ← Business logic lives here
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php                    ← Extend: `schoolmanagement::layouts.app`
│       └── pages/
│           └── posts/
│               ├── index.blade.php
│               ├── create.blade.php
│               ├── edit.blade.php
│               ├── show.blade.php
│               └── partials/
│                   └── form.blade.php           ← Shared create/edit form partial
├── routes/
│   └── web.php
└── tests/
    └── Feature/
```

### Key Design Patterns

**1. Service Layer** — Business logic in `Services/`, controllers stay thin.

```php
// Controller: authorize → validate → delegate → redirect
public function store(PostRequest $request): RedirectResponse
{
    $this->postService->create($request->validated());
    return redirect()->route('admin.schoolmanagement.posts.index')
        ->with('success', __('Created successfully.'));
}
```

**2. FormRequest Validation** — Every write action uses a dedicated `*Request` class.

**3. Thin Controllers** — Controllers only handle: authorize → validate → call service → redirect.

**4. Policies** — `PostPolicy` registered in `SchoolManagementServiceProvider::boot()` via `Gate::policy()`.

**5. Livewire Datatables** — Only for listing pages. Create / Edit / Show use plain Blade + form submit.

**6. HasBreadcrumbs Trait** — Base controller provides `setBreadcrumbTitle()` and `renderViewWithBreadcrumbs()`.

### Routing Convention

All web routes live in `routes/web.php` under the admin middleware group:

```php
Route::prefix('admin/schoolmanagement')
    ->middleware(['web', 'auth', 'permission:schoolmanagement.dashboard'])
    ->name('admin.schoolmanagement.')
    ->group(function () {
        Route::resource('posts', PostController::class);
        // Add more resources here
    });
```

Named route format: `admin.schoolmanagement.{resource}.{action}`
URL format: `/admin/schoolmanagement/{resource}`

### View Namespace

```blade
{{-- Extend the module layout --}}
@extends('schoolmanagement::layouts.master')

{{-- Reference views by namespace --}}
return view('schoolmanagement::pages.posts.index', compact('posts'));
```

### Tailwind CSS Prefix

This module uses `prefix(schoolmanagement)` in its Tailwind CSS config. Every Tailwind utility class in module views **must** be prefixed with `schoolmanagement:`:

```blade
{{-- Correct — prefixed --}}
<div class="schoolmanagement:py-16 schoolmanagement:text-center schoolmanagement:flex schoolmanagement:items-center">

{{-- Wrong — no prefix, class won't be generated by the module CSS --}}
<div class="py-16 text-center flex items-center">
```

The module CSS is built separately and loaded via `@vite` in `resources/views/layouts/master.blade.php`.
**After creating a new module**, run once from the module directory:
```bash
cd modules/SchoolManagement && npm install && npm run build
```

Shared component classes (`btn`, `btn-primary`, `form-control`, etc.) come from the main app CSS — use them **without** the prefix.

---

## Permissions

Permissions follow the pattern `schoolmanagement.{resource}.{action}`:

```php
// In a permission migration (generated by module:make-crud)
Permission::create(['name' => 'schoolmanagement.posts.index']);
Permission::create(['name' => 'schoolmanagement.posts.create']);
Permission::create(['name' => 'schoolmanagement.posts.edit']);
Permission::create(['name' => 'schoolmanagement.posts.delete']);
```

Check permissions in controllers or Blade:

```php
// Policy (preferred)
$this->authorize('update', $post);

// Gate check
abort_unless(auth()->user()->can('schoolmanagement.posts.edit'), 403);
```

```blade
@can('schoolmanagement.posts.create')
    <a href="{{ route('admin.schoolmanagement.posts.create') }}">New Post</a>
@endcan
```

---

## Coding Conventions

### Strict Types

Every PHP class must have `declare(strict_types=1);` at the top:

```php
<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;
```

### Model

```php
class Post extends Model
{
    protected $fillable = ['title', 'body', 'status', 'is_featured'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
```

### Service

```php
class PostService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return Post::query()
            ->when($filters['search'] ?? null, fn ($q, $s) =>
                $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }
}
```

### Controller

```php
class PostController extends SchoolManagementController
{
    public function __construct(private readonly PostService $postService)
    {
        $this->setBreadcrumbTitle(__('Posts'));
    }

    public function index(): View
    {
        $this->authorize('viewAny', Post::class);
        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.posts.index');
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);
        $this->postService->create($request->validated());
        return redirect()->route('admin.schoolmanagement.posts.index')
            ->with('success', __('Post created.'));
    }
}
```

---

## UI Components

Use Lara Dashboard's shared CSS classes — defined in `resources/css/components.css`:

```blade
{{-- Text input --}}
<x-inputs.input name="title" label="{{ __('Title') }}" :required="true" />

{{-- Select dropdown --}}
<x-inputs.select
    name="status"
    label="{{ __('Status') }}"
    :options="['draft' => 'Draft', 'published' => 'Published']"
/>

{{-- Toggle switch --}}
<x-inputs.toggle name="is_featured" label="{{ __('Featured') }}" />

{{-- Media library selector --}}
<x-media-selector
    name="thumbnail_id"
    label="{{ __('Thumbnail') }}"
    :multiple="false"
    allowedTypes="images"
/>

{{-- Raw classes --}}
<input class="form-control" />
<textarea class="form-control-textarea"></textarea>
<label class="form-label">Label</label>
<button class="btn btn-primary">Save</button>
<button class="btn btn-default">Cancel</button>
<button class="btn btn-danger">Delete</button>
```

---

## Testing

Feature tests live in `modules/SchoolManagement/tests/Feature/`.

```php
<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Tests\Feature;

use Tests\TestCase;
use Modules\SchoolManagement\Models\Post;

class PostControllerTest extends TestCase
{
    public function test_index_is_accessible(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.schoolmanagement.posts.index'))
            ->assertOk();
    }

    public function test_can_create_post(): void
    {
        $data = Post::factory()->make()->toArray();

        $this->actingAsAdmin()
            ->post(route('admin.schoolmanagement.posts.store'), $data)
            ->assertRedirect(route('admin.schoolmanagement.posts.index'));

        $this->assertDatabaseHas('schoolmanagement_posts', ['title' => $data['title']]);
    }

    public function test_can_update_post(): void
    {
        $post = Post::factory()->create();

        $this->actingAsAdmin()
            ->put(route('admin.schoolmanagement.posts.update', $post), ['title' => 'Updated'])
            ->assertRedirect(route('admin.schoolmanagement.posts.index'));

        $this->assertDatabaseHas('schoolmanagement_posts', ['id' => $post->id, 'title' => 'Updated']);
    }

    public function test_can_delete_post(): void
    {
        $post = Post::factory()->create();

        $this->actingAsAdmin()
            ->delete(route('admin.schoolmanagement.posts.destroy', $post))
            ->assertRedirect(route('admin.schoolmanagement.posts.index'));

        $this->assertDatabaseMissing('schoolmanagement_posts', ['id' => $post->id]);
    }
}
```

---

## Checklist for a New CRUD Resource

- [ ] Migration created and run (`php artisan migrate`)
- [ ] Model has correct `$fillable` and `casts()`
- [ ] Service class has `getAll`, `create`, `update`, `delete`
- [ ] Controller is thin (delegates to service)
- [ ] FormRequest has validation rules for all fields
- [ ] Policy created and registered in ServiceProvider
- [ ] Permission migration created and run
- [ ] Routes added to `routes/web.php`
- [ ] Menu item added to `MenuService`
- [ ] Views reviewed (index, create, edit, show, form partial)
- [ ] Tests written and passing (`php artisan test --filter=PostController`)
- [ ] Code style fixed (`vendor/bin/pint --dirty`)

---

## Important Reminders

1. **Module isolation** — This module must work independently. No hardcoded dependencies on other modules.
2. **Permissions always** — Check permissions in every controller action or via policy.
3. **Service layer** — Business logic goes in `Services/`. Never in controllers or Blade.
4. **Eager loading** — Always use `->with([...])` to prevent N+1 queries.
5. **Translations** — Use `__('schoolmanagement::messages.key')` for module-scoped strings.
6. **Run Pint** — Always run `vendor/bin/pint --dirty` before committing.
7. **SOLID / DRY / KISS** — Keep it simple; avoid premature abstraction.
