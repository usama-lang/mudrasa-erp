<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreTeacherRequest;
use Modules\SchoolManagement\Http\Requests\UpdateTeacherRequest;
use Modules\SchoolManagement\Models\Teacher;
use Modules\SchoolManagement\Services\CampusService;
use Modules\SchoolManagement\Services\SchoolScopeService;
use Modules\SchoolManagement\Services\TeacherService;

class TeacherController extends SchoolManagementController
{
    public function __construct(
        private readonly TeacherService $teacherService,
        private readonly CampusService $campusService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_teacher.view'), 403);

        $this->setBreadcrumbTitle(bi('Teachers'))
            ->setBreadcrumbIcon('lucide:user-round-pen')
            ->setBreadcrumbActionButton(
                route('school.teachers.create'),
                bi('New Teacher'),
                'lucide:plus',
                'school_teacher.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.teachers.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_teacher.view'), 403);

        $query = Teacher::query()->with(['campus', 'user']);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        } elseif ($request->filled('campus')) {
            $query->where('campus_id', $request->integer('campus'));
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhereHas(
                      'user',
                      fn ($u) =>
                      $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sortable = ['id', 'employee_id', 'designation', 'joining_date', 'status'];
        $sort = in_array($request->string('sort')->value(), $sortable) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $paginated = $query->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn ($t) => [
            'id' => $t->id,
            'full_name' => $t->user?->full_name ?? '-',
            'employee_id' => $t->employee_id,
            'campus_name' => $t->campus?->name ?? '-',
            'designation' => $t->designation ?? '-',
            'status' => $t->status,
            'joining_date' => $t->joining_date?->format('Y-m-d') ?? '-',
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
        abort_unless(auth()->user()->can('school_teacher.create'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('New Teacher'))
            ->setBreadcrumbIcon('lucide:user-round-pen')
            ->addBreadcrumbItem(bi('Teachers'), route('school.teachers.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.teachers.create', [
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'employeeId' => $this->teacherService->generateEmployeeId(),
        ]);
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $this->teacherService->createWithUser($request->validated());

        session()->flash('success', bi('Teacher created successfully.'));

        return redirect()->route('school.teachers.index');
    }

    public function show(int $teacher): Renderable
    {
        $teacherModel = Teacher::with(['campus', 'user'])->findOrFail($teacher);

        abort_unless(auth()->user()->can('school_teacher.view'), 403);

        $this->setBreadcrumbTitle($teacherModel->user?->full_name ?? bi('Teacher'))
            ->setBreadcrumbIcon('lucide:user-round-pen')
            ->addBreadcrumbItem(bi('Teachers'), route('school.teachers.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.teachers.show', [
            'teacher' => $teacherModel,
        ]);
    }

    public function edit(int $teacher): Renderable
    {
        $teacherModel = Teacher::with('user')->findOrFail($teacher);

        abort_unless(auth()->user()->can('school_teacher.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('Edit Teacher'))
            ->setBreadcrumbIcon('lucide:user-round-pen')
            ->addBreadcrumbItem(bi('Teachers'), route('school.teachers.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.teachers.edit', [
            'teacher' => $teacherModel,
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
        ]);
    }

    public function update(UpdateTeacherRequest $request, int $teacher): RedirectResponse
    {
        $this->teacherService->updateWithUser($teacher, $request->validated());

        session()->flash('success', bi('Teacher updated successfully.'));

        return redirect()->route('school.teachers.index');
    }

    public function destroy(Request $request, int $teacher): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('school_teacher.delete'), 403);

        $this->teacherService->delete($teacher);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        session()->flash('success', bi('Teacher deleted successfully.'));

        return redirect()->route('school.teachers.index');
    }
}
