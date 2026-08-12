<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\MadrasaFunds\Http\Requests\StoreDepartmentRequest;
use Modules\MadrasaFunds\Http\Requests\UpdateDepartmentRequest;
use Modules\MadrasaFunds\Models\Department;
use Modules\MadrasaFunds\Services\DepartmentService;

class DepartmentController extends MadrasaFundsController
{
    public function __construct(
        private readonly DepartmentService $departmentService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_departments'), 403);

        $this->setBreadcrumbTitle(mf_bi('Departments'))
            ->setBreadcrumbIcon('lucide:layout-list')
            ->setBreadcrumbActionButton(
                route('admin.madrasafunds.departments.create'),
                mf_bi('New Department'),
                'lucide:plus',
                'madrasa_funds.manage_departments'
            );

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.departments.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_departments'), 403);

        $query = Department::query()->withCount('students');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_urdu', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->value() === 'active');
        }

        $sortable = ['id', 'name', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable, true) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        $paginated = $query->orderBy($sort, $dir)->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn (Department $d) => [
            'id' => $d->id,
            'name' => $d->name,
            'name_urdu' => $d->name_urdu,
            'students_count' => $d->students_count,
            'status' => $d->is_active ? 'active' : 'inactive',
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    public function create(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_departments'), 403);

        $this->setBreadcrumbTitle(mf_bi('New Department'))
            ->setBreadcrumbIcon('lucide:layout-list')
            ->addBreadcrumbItem(mf_bi('Departments'), route('admin.madrasafunds.departments.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.departments.create', [
            'department' => null,
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->create($request->validated());

        session()->flash('success', mf_bi('Department created successfully.'));

        return redirect()->route('admin.madrasafunds.departments.index');
    }

    public function edit(Department $department): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_departments'), 403);

        $this->setBreadcrumbTitle(mf_bi('Edit Department'))
            ->setBreadcrumbIcon('lucide:layout-list')
            ->addBreadcrumbItem(mf_bi('Departments'), route('admin.madrasafunds.departments.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.departments.edit', [
            'department' => $department,
        ]);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $this->departmentService->update($department->id, $request->validated());

        session()->flash('success', mf_bi('Department updated successfully.'));

        return redirect()->route('admin.madrasafunds.departments.index');
    }

    public function destroy(Department $department): RedirectResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_departments'), 403);

        $this->departmentService->delete($department->id);

        session()->flash('success', mf_bi('Department deleted successfully.'));

        return redirect()->route('admin.madrasafunds.departments.index');
    }
}
