<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreDepartmentRequest;
use Modules\SchoolManagement\Http\Requests\UpdateDepartmentRequest;
use Modules\SchoolManagement\Models\Department;
use Modules\SchoolManagement\Services\CampusService;
use Modules\SchoolManagement\Services\DepartmentService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class DepartmentController extends SchoolManagementController
{
    public function __construct(
        private readonly DepartmentService $departmentService,
        private readonly CampusService $campusService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_department.view'), 403);

        $this->setBreadcrumbTitle(bi('Departments'))
            ->setBreadcrumbIcon('lucide:layout-list')
            ->setBreadcrumbActionButton(
                route('school.departments.create'),
                bi('New Department'),
                'lucide:plus',
                'school_department.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.departments.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_department.view'), 403);

        $query = Department::query()->with('campus');

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        } elseif ($request->filled('campus')) {
            $query->where('campus_id', $request->integer('campus'));
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sortable = ['id', 'name', 'code', 'status', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $paginated = $query->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'code' => $d->code,
            'campus_name' => $d->campus?->name ?? '-',
            'status' => $d->status,
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
        abort_unless(auth()->user()->can('school_department.create'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('New Department'))
            ->setBreadcrumbIcon('lucide:layout-list')
            ->addBreadcrumbItem(bi('Departments'), route('school.departments.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.departments.create', [
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->create($request->validated());

        session()->flash('success', bi('Department created successfully.'));

        return redirect()->route('school.departments.index');
    }

    public function show(int $department): Renderable
    {
        $dept = Department::with(['campus', 'classes'])->findOrFail($department);

        abort_unless(auth()->user()->can('school_department.view'), 403);

        $this->setBreadcrumbTitle($dept->name)
            ->setBreadcrumbIcon('lucide:layout-list')
            ->addBreadcrumbItem(bi('Departments'), route('school.departments.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.departments.show', [
            'department' => $dept,
        ]);
    }

    public function edit(int $department): Renderable
    {
        $dept = Department::findOrFail($department);

        abort_unless(auth()->user()->can('school_department.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('Edit Department'))
            ->setBreadcrumbIcon('lucide:layout-list')
            ->addBreadcrumbItem(bi('Departments'), route('school.departments.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.departments.edit', [
            'department' => $dept,
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
        ]);
    }

    public function update(UpdateDepartmentRequest $request, int $department): RedirectResponse
    {
        $this->departmentService->update($department, $request->validated());

        session()->flash('success', bi('Department updated successfully.'));

        return redirect()->route('school.departments.index');
    }

    public function destroy(Request $request, int $department): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('school_department.delete'), 403);

        $this->departmentService->delete($department);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        session()->flash('success', bi('Department deleted successfully.'));

        return redirect()->route('school.departments.index');
    }
}
