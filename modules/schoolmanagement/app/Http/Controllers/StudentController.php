<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreStudentRequest;
use Modules\SchoolManagement\Http\Requests\UpdateParaQuantityRequest;
use Modules\SchoolManagement\Http\Requests\UpdateStudentRequest;
use Modules\SchoolManagement\Models\Student;
use Modules\SchoolManagement\Services\CampusService;
use Modules\SchoolManagement\Services\DepartmentService;
use Modules\SchoolManagement\Services\SchoolClassService;
use Modules\SchoolManagement\Services\SchoolScopeService;
use Modules\SchoolManagement\Services\SectionService;
use Modules\SchoolManagement\Services\StudentService;

class StudentController extends SchoolManagementController
{
    public function __construct(
        private readonly StudentService $studentService,
        private readonly CampusService $campusService,
        private readonly DepartmentService $departmentService,
        private readonly SchoolClassService $classService,
        private readonly SectionService $sectionService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_student.view'), 403);

        $this->setBreadcrumbTitle(bi('Students'))
            ->setBreadcrumbIcon('lucide:graduation-cap')
            ->setBreadcrumbActionButton(
                route('school.students.create'),
                bi('New Student'),
                'lucide:plus',
                'school_student.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.students.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_student.view'), 403);

        $query = Student::query()->with(['campus', 'department', 'schoolClass', 'section']);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        } elseif ($request->filled('campus')) {
            $query->where('campus_id', $request->integer('campus'));
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('class')) {
            $query->where('class_id', $request->integer('class'));
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('phone_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->integer('status'));
        }

        $sortable = ['id', 'student_name', 'admission_no', 'status', 'admission_date'];
        $sort = in_array($request->string('sort')->value(), $sortable) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $paginated = $query->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn (Student $s) => [
            'id' => $s->id,
            'student_name' => $s->student_name ?? '-',
            'admission_no' => $s->admission_no,
            'campus_name' => $s->campus?->name ?? '-',
            'department_name' => $s->department?->name ?? '-',
            'class_name' => $s->schoolClass?->name ?? '-',
            'section_name' => $s->section?->name ?? '-',
            'phone_no' => $s->phone_no ?? '-',
            'para_quantity' => $s->para_quantity !== null ? (float) $s->para_quantity : null,
            'status' => $s->status,
            'enrollment_status' => $s->enrollment_status ?? 'enrolled',
            'admission_date' => $s->admission_date?->format('Y-m-d') ?? '-',
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
        abort_unless(auth()->user()->can('school_student.create'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('New Student'))
            ->setBreadcrumbIcon('lucide:graduation-cap')
            ->addBreadcrumbItem(bi('Students'), route('school.students.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.students.create', [
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'departments' => $this->departmentService->getDepartmentsDropdown($campusId),
            'classes' => $this->classService->getClassesDropdown($campusId),
            'sections' => $this->sectionService->getSectionsDropdown($campusId),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->studentService->create($request->validated());

        session()->flash('success', bi('Student admitted successfully.'));

        return redirect()->route('school.students.index');
    }

    public function show(int $student): Renderable
    {
        /** @var Student $studentModel */
        $studentModel = Student::with(['campus', 'department', 'schoolClass', 'section'])->findOrFail($student);

        abort_unless(auth()->user()->can('school_student.view'), 403);

        $this->setBreadcrumbTitle($studentModel->student_name ?? bi('Student'))
            ->setBreadcrumbIcon('lucide:graduation-cap')
            ->addBreadcrumbItem(bi('Students'), route('school.students.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.students.show', [
            'student' => $studentModel,
        ]);
    }

    public function edit(int $student): Renderable
    {
        /** @var Student $studentModel */
        $studentModel = Student::findOrFail($student);

        abort_unless(auth()->user()->can('school_student.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('Edit Student'))
            ->setBreadcrumbIcon('lucide:graduation-cap')
            ->addBreadcrumbItem(bi('Students'), route('school.students.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.students.edit', [
            'student' => $studentModel,
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'departments' => $this->departmentService->getDepartmentsDropdown($studentModel->campus_id),
            'classes' => $this->classService->getClassesDropdown($studentModel->campus_id),
            'sections' => $this->sectionService->getSectionsDropdown($studentModel->campus_id, $studentModel->class_id),
        ]);
    }

    public function update(UpdateStudentRequest $request, int $student): RedirectResponse
    {
        $this->studentService->update($student, $request->validated());

        session()->flash('success', bi('Student updated successfully.'));

        return redirect()->route('school.students.index');
    }

    public function updateParaQuantity(UpdateParaQuantityRequest $request, int $student): JsonResponse
    {
        $studentModel = $this->studentService->updateParaQuantity($student, (float) $request->validated()['para_quantity']);

        return response()->json([
            'success' => true,
            'para_quantity' => $studentModel->para_quantity !== null ? (float) $studentModel->para_quantity : null,
        ]);
    }

    public function destroy(Request $request, int $student): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('school_student.delete'), 403);

        $this->studentService->delete($student);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        session()->flash('success', bi('Student deleted successfully.'));

        return redirect()->route('school.students.index');
    }
}
