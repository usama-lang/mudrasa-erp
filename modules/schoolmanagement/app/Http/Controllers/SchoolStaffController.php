<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\CampusUser;
use Modules\SchoolManagement\Services\CampusUserService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class SchoolStaffController extends SchoolManagementController
{
    public function __construct(private readonly CampusUserService $campusUserService)
    {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('school_manager.view'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $query = CampusUser::query()->with(['campus', 'user', 'creator']);

        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        }

        $staff = $query->orderBy('created_at', 'desc')->paginate(15);

        $this->setBreadcrumbTitle(bi('Staff Management'))
            ->setBreadcrumbIcon('lucide:users')
            ->setBreadcrumbActionButton(
                route('school.staff.create'),
                bi('Add Staff'),
                'lucide:plus',
                'school_manager.create'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.staff.index', [
            'staff' => $staff,
        ]);
    }

    public function create(): Renderable
    {
        abort_unless(auth()->user()->can('school_manager.create'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $this->setBreadcrumbTitle(bi('Add Staff Member'))
            ->setBreadcrumbIcon('lucide:users')
            ->addBreadcrumbItem(bi('Staff'), route('school.staff.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.staff.create', [
            'campuses'           => Campus::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'campusId'           => $campusId,
            'grantablePermissions' => $this->campusUserService->getGrantablePermissions(auth()->user()),
            'roleTypes'          => $this->getRoleTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('school_manager.create'), 403);

        $data = $request->validate([
            'campus_id'    => ['required', 'integer', 'exists:school_campuses,id'],
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8'],
            'role_type'    => ['required', 'in:main_manager,assistant_manager,account_manager,assistant_account_manager,exam_manager,exam_assistant_manager,teacher'],
            'permissions'  => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $this->campusUserService->create($data, auth()->user());

        session()->flash('success', bi('Staff member added successfully.'));

        return redirect()->route('school.staff.index');
    }

    public function edit(CampusUser $staff): Renderable
    {
        abort_unless(auth()->user()->can('school_manager.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null && $campusId !== $staff->campus_id) {
            abort(403);
        }

        $this->setBreadcrumbTitle(bi('Edit Staff Member'))
            ->setBreadcrumbIcon('lucide:users')
            ->addBreadcrumbItem(bi('Staff'), route('school.staff.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.staff.edit', [
            'staff'              => $staff->load(['campus', 'user']),
            'grantablePermissions' => $this->campusUserService->getGrantablePermissions(auth()->user()),
            'roleTypes'          => $this->getRoleTypes(),
        ]);
    }

    public function update(Request $request, CampusUser $staff): RedirectResponse
    {
        abort_unless(auth()->user()->can('school_manager.edit'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null && $campusId !== $staff->campus_id) {
            abort(403);
        }

        $data = $request->validate([
            'role_type'     => ['required', 'in:main_manager,assistant_manager,account_manager,assistant_account_manager,exam_manager,exam_assistant_manager,teacher'],
            'is_active'     => ['nullable', 'boolean'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $this->campusUserService->update($staff, $data, auth()->user());

        session()->flash('success', bi('Staff member updated successfully.'));

        return redirect()->route('school.staff.index');
    }

    public function destroy(CampusUser $staff): RedirectResponse
    {
        abort_unless(auth()->user()->can('school_manager.delete'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null && $campusId !== $staff->campus_id) {
            abort(403);
        }

        $this->campusUserService->deactivate($staff);

        session()->flash('success', bi('Staff member deactivated successfully.'));

        return redirect()->route('school.staff.index');
    }

    private function getRoleTypes(): array
    {
        return [
            'main_manager'              => bi('Main Manager'),
            'assistant_manager'         => bi('Assistant Manager'),
            'account_manager'           => bi('Account Manager'),
            'assistant_account_manager' => bi('Assistant Account Manager'),
            'exam_manager'              => bi('Exam Manager'),
            'exam_assistant_manager'    => bi('Exam Assistant Manager'),
            'teacher'                   => bi('Teacher'),
        ];
    }
}
