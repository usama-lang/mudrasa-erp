<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\MadrasaFunds\Http\Requests\StoreStudentRequest;
use Modules\MadrasaFunds\Http\Requests\UpdateStudentRequest;
use Modules\MadrasaFunds\Models\Department;
use Modules\MadrasaFunds\Models\Student;
use Modules\MadrasaFunds\Services\DepartmentService;
use Modules\MadrasaFunds\Services\StudentService;

class StudentController extends MadrasaFundsController
{
    public function __construct(
        private readonly StudentService $studentService,
        private readonly DepartmentService $departmentService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_any'), 403);

        $this->setBreadcrumbTitle(mf_bi('Students'))
            ->setBreadcrumbIcon('lucide:users')
            ->setBreadcrumbActionButton(
                route('admin.madrasafunds.students.create'),
                mf_bi('New Student'),
                'lucide:plus',
                'madrasa_funds.create'
            );

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.students.index', [
            'departments' => Department::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_any'), 403);

        $query = Student::query()->with('department');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('class', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->value() === 'active');
        }

        $sortable = ['id', 'name', 'class', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable, true) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        $paginated = $query->orderBy($sort, $dir)->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn (Student $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'father_name' => $s->father_name ?? '-',
            'class' => $s->class ?? '-',
            'department_name' => $s->department?->name ?? '-',
            'status' => $s->is_active ? 'active' : 'inactive',
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
        abort_unless(auth()->user()->can('madrasa_funds.create'), 403);

        $this->setBreadcrumbTitle(mf_bi('New Student'))
            ->setBreadcrumbIcon('lucide:user-plus')
            ->addBreadcrumbItem(mf_bi('Students'), route('admin.madrasafunds.students.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.students.create', [
            'student' => null,
            'departments' => $this->departmentService->getDepartmentsDropdown(),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->studentService->create($request->validated());

        session()->flash('success', mf_bi('Student created successfully.'));

        return redirect()->route('admin.madrasafunds.students.index');
    }

    public function edit(Student $student): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.edit'), 403);

        $this->setBreadcrumbTitle(mf_bi('Edit Student'))
            ->setBreadcrumbIcon('lucide:user-pen')
            ->addBreadcrumbItem(mf_bi('Students'), route('admin.madrasafunds.students.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.students.edit', [
            'student' => $student,
            'departments' => $this->departmentService->getDepartmentsDropdown(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $this->studentService->update($student->id, $request->validated());

        session()->flash('success', mf_bi('Student updated successfully.'));

        return redirect()->route('admin.madrasafunds.students.index');
    }

    public function destroy(Student $student): RedirectResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.delete'), 403);

        $this->studentService->delete($student->id);

        session()->flash('success', mf_bi('Student deleted successfully.'));

        return redirect()->route('admin.madrasafunds.students.index');
    }
}
