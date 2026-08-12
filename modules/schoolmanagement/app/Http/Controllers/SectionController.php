<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreSectionRequest;
use Modules\SchoolManagement\Http\Requests\UpdateSectionRequest;
use Modules\SchoolManagement\Models\Section;
use Modules\SchoolManagement\Services\CampusService;
use Modules\SchoolManagement\Services\SchoolClassService;
use Modules\SchoolManagement\Services\SchoolScopeService;
use Modules\SchoolManagement\Services\SectionService;

class SectionController extends SchoolManagementController
{
    public function __construct(
        private readonly SectionService $sectionService,
        private readonly CampusService $campusService,
        private readonly SchoolClassService $classService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_section.view'), 403);

        $this->setBreadcrumbTitle(bi('Sections'))
            ->setBreadcrumbIcon('lucide:layers')
            ->setBreadcrumbActionButton(
                route('school.sections.create'),
                bi('New Section'),
                'lucide:plus',
                'school_section.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.sections.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_section.view'), 403);

        $query = Section::query()->with(['campus', 'schoolClass']);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        } elseif ($request->filled('campus')) {
            $query->where('campus_id', $request->integer('campus'));
        }

        if ($request->filled('class')) {
            $query->where('class_id', $request->integer('class'));
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sortable = ['id', 'name', 'capacity', 'status', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $paginated = $query->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'campus_name' => $s->campus?->name ?? '-',
            'class_name' => $s->schoolClass?->name ?? '-',
            'capacity' => $s->capacity,
            'status' => $s->status,
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
        abort_unless(auth()->user()->can('school_section.create'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('New Section'))
            ->setBreadcrumbIcon('lucide:layers')
            ->addBreadcrumbItem(bi('Sections'), route('school.sections.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.sections.create', [
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'classes' => $this->classService->getClassesDropdown($campusId),
        ]);
    }

    public function store(StoreSectionRequest $request): RedirectResponse
    {
        $this->sectionService->create($request->validated());

        session()->flash('success', bi('Section created successfully.'));

        return redirect()->route('school.sections.index');
    }

    public function show(int $section): Renderable
    {
        $sectionModel = Section::with(['campus', 'schoolClass', 'students'])->findOrFail($section);

        abort_unless(auth()->user()->can('school_section.view'), 403);

        $this->setBreadcrumbTitle($sectionModel->name)
            ->setBreadcrumbIcon('lucide:layers')
            ->addBreadcrumbItem(bi('Sections'), route('school.sections.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.sections.show', [
            'section' => $sectionModel,
        ]);
    }

    public function edit(int $section): Renderable
    {
        $sectionModel = Section::findOrFail($section);

        abort_unless(auth()->user()->can('school_section.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('Edit Section'))
            ->setBreadcrumbIcon('lucide:layers')
            ->addBreadcrumbItem(bi('Sections'), route('school.sections.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.sections.edit', [
            'section' => $sectionModel,
            'campuses' => $this->campusService->getCampusesDropdown($campusId),
            'classes' => $this->classService->getClassesDropdown($sectionModel->campus_id),
        ]);
    }

    public function update(UpdateSectionRequest $request, int $section): RedirectResponse
    {
        $this->sectionService->update($section, $request->validated());

        session()->flash('success', bi('Section updated successfully.'));

        return redirect()->route('school.sections.index');
    }

    public function destroy(Request $request, int $section): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('school_section.delete'), 403);

        $this->sectionService->delete($section);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        session()->flash('success', bi('Section deleted successfully.'));

        return redirect()->route('school.sections.index');
    }
}
