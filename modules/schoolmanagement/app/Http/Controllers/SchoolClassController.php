<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreSchoolClassRequest;
use Modules\SchoolManagement\Http\Requests\UpdateSchoolClassRequest;
use Modules\SchoolManagement\Models\SchoolClass;
use Modules\SchoolManagement\Services\CampusService;
use Modules\SchoolManagement\Services\DepartmentService;
use Modules\SchoolManagement\Services\SchoolClassService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class SchoolClassController extends SchoolManagementController
{
    public function __construct(
        private readonly SchoolClassService $classService,
        private readonly CampusService $campusService,
        private readonly DepartmentService $departmentService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_class.view'), 403);

        $this->setBreadcrumbTitle(bi('Classes'))
            ->setBreadcrumbIcon('lucide:book-open')
            ->setBreadcrumbActionButton(
                route('school.classes.create'),
                bi('New Class'),
                'lucide:plus',
                'school_class.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.classes.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_class.view'), 403);

        $query = SchoolClass::query()->with(['campus', 'department']);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        } elseif ($request->filled('campus')) {
            $query->where('campus_id', $request->integer('campus'));
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sortable = ['id', 'name', 'numeric_name', 'status', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $paginated = $query->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'numeric_name' => $c->numeric_name,
            'campus_name' => $c->campus?->name ?? '-',
            'department_name' => $c->department?->name ?? '-',
            'status' => $c->status,
            'created_at' => $c->created_at?->format('Y-m-d'),
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
        abort_unless(auth()->user()->can('school_class.create'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('New Class'))
            ->setBreadcrumbIcon('lucide:book-open')
            ->addBreadcrumbItem(bi('Classes'), route('school.classes.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.classes.create', [
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'departments' => $this->departmentService->getDepartmentsDropdown($campusId),
        ]);
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        $this->classService->create($request->validated());

        session()->flash('success', bi('Class created successfully.'));

        return redirect()->route('school.classes.index');
    }

    public function show(int $class): Renderable
    {
        $schoolClass = SchoolClass::with(['campus', 'department', 'sections', 'students'])->findOrFail($class);

        abort_unless(auth()->user()->can('school_class.view'), 403);

        $this->setBreadcrumbTitle($schoolClass->name)
            ->setBreadcrumbIcon('lucide:book-open')
            ->addBreadcrumbItem(bi('Classes'), route('school.classes.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.classes.show', [
            'schoolClass' => $schoolClass,
        ]);
    }

    public function edit(int $class): Renderable
    {
        $schoolClass = SchoolClass::findOrFail($class);

        abort_unless(auth()->user()->can('school_class.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('Edit Class'))
            ->setBreadcrumbIcon('lucide:book-open')
            ->addBreadcrumbItem(bi('Classes'), route('school.classes.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.classes.edit', [
            'schoolClass' => $schoolClass,
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'departments' => $this->departmentService->getDepartmentsDropdown($schoolClass->campus_id),
        ]);
    }

    public function update(UpdateSchoolClassRequest $request, int $class): RedirectResponse
    {
        $this->classService->update($class, $request->validated());

        session()->flash('success', bi('Class updated successfully.'));

        return redirect()->route('school.classes.index');
    }

    public function destroy(Request $request, int $class): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('school_class.delete'), 403);

        $this->classService->delete($class);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        session()->flash('success', bi('Class deleted successfully.'));

        return redirect()->route('school.classes.index');
    }
}
