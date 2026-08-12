<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreCampusRequest;
use Modules\SchoolManagement\Http\Requests\UpdateCampusRequest;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Services\CampusService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class CampusController extends SchoolManagementController
{
    public function __construct(private readonly CampusService $campusService)
    {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_campus.view'), 403);

        $this->setBreadcrumbTitle(bi('Campuses'))
            ->setBreadcrumbIcon('lucide:building-2')
            ->setBreadcrumbActionButton(
                route('school.campuses.create'),
                bi('New Campus'),
                'lucide:plus',
                'school_campus.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.campuses.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_campus.view'), 403);

        $query = Campus::query()->withCount(['departments', 'students', 'teachers']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sortable = ['id', 'name', 'code', 'status', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $paginated = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $paginated->items(),
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
        abort_unless(auth()->user()->can('school_campus.create'), 403);

        $this->setBreadcrumbTitle(bi('New Campus'))
            ->setBreadcrumbIcon('lucide:building-2')
            ->addBreadcrumbItem(bi('Campuses'), route('school.campuses.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.campuses.create', [
            'managers' => \App\Models\User::orderBy('first_name')->get()->pluck('full_name', 'id')->toArray(),
        ]);
    }

    public function store(StoreCampusRequest $request): RedirectResponse
    {
        $this->campusService->create($request->validated());

        session()->flash('success', bi('Campus created successfully.'));

        return redirect()->route('school.campuses.index');
    }

    public function show(int $campus): Renderable
    {
        $campusModel = Campus::with(['manager', 'departments', 'teachers', 'students'])->findOrFail($campus);

        abort_unless(auth()->user()->can('school_campus.view'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null && $campusId !== $campusModel->id) {
            abort(403);
        }

        $this->setBreadcrumbTitle($campusModel->name)
            ->setBreadcrumbIcon('lucide:building-2')
            ->addBreadcrumbItem(bi('Campuses'), route('school.campuses.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.campuses.show', [
            'campus' => $campusModel,
        ]);
    }

    public function edit(int $campus): Renderable
    {
        $campusModel = Campus::findOrFail($campus);

        abort_unless(auth()->user()->can('school_campus.edit'), 403);

        $this->setBreadcrumbTitle(bi('Edit Campus'))
            ->setBreadcrumbIcon('lucide:building-2')
            ->addBreadcrumbItem(bi('Campuses'), route('school.campuses.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.campuses.edit', [
            'campus' => $campusModel,
            'managers' => \App\Models\User::orderBy('first_name')->get()->pluck('full_name', 'id')->toArray(),
        ]);
    }

    public function update(UpdateCampusRequest $request, int $campus): RedirectResponse
    {
        $this->campusService->update($campus, $request->validated());

        session()->flash('success', bi('Campus updated successfully.'));

        return redirect()->route('school.campuses.index');
    }

    public function destroy(Request $request, int $campus): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->can('school_campus.delete'), 403);

        $this->campusService->delete($campus);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        session()->flash('success', bi('Campus deleted successfully.'));

        return redirect()->route('school.campuses.index');
    }
}
