<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('schoolmanagement::partials.school-datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ bi('Search students...') }}">
            <div class="flex items-center gap-2 flex-wrap">
                @if(auth()->user()->hasRole(['superadmin','admin']))
                <select id="dt-campus" class="form-control text-sm">
                    <option value="">{{ bi('All Campuses') }}</option>
                    @foreach(\Modules\SchoolManagement\Models\Campus::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @endif
                <select id="dt-department" class="form-control text-sm">
                    <option value="">{{ bi('All Departments') }}</option>
                    @foreach(\Modules\SchoolManagement\Models\Department::orderBy('name')->get() as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                <select id="dt-class" class="form-control text-sm">
                    <option value="">{{ bi('All Classes') }}</option>
                    @foreach(\Modules\SchoolManagement\Models\SchoolClass::orderBy('name')->get() as $cl)
                    <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                    @endforeach
                </select>
                <select id="dt-status" class="form-control text-sm">
                    <option value="">{{ bi('All Status') }}</option>
                    <option value="1">{{ bi('Active') }}</option>
                    <option value="0">{{ bi('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="school-student-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="student_name">{{ bi('Name') }}</th>
                        <th class="px-4 py-3" data-sort="admission_no">{{ bi('Admission No.') }}</th>
                        <th class="px-4 py-3">{{ bi('Campus') }}</th>
                        <th class="px-4 py-3">{{ bi('Department') }}</th>
                        <th class="px-4 py-3">{{ bi('Class') }}</th>
                        <th class="px-4 py-3">{{ bi('Phone') }}</th>
                        <th class="px-4 py-3">{{ bi('Para Quantity') }}</th>
                        <th class="px-4 py-3">{{ bi('Enrollment Status') }}</th>
                        <th class="px-4 py-3" data-sort="status">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="school-student-dt-body">
                    <tr><td colspan="10" class="text-center py-8 text-gray-500">{{ bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="school-student-dt-pagination" class="mt-4"></div>
    </x-card>

    @can('school_student.edit')
        @include('schoolmanagement::pages.students.partials.para-quantity-modal')
    @endcan

    @push('scripts')
    <script>
    const studentDt = new SchoolDatatable({
        url: '{{ route('school.students.data') }}',
        tableId: 'school-student-dt',
        bodyId: 'school-student-dt-body',
        paginationId: 'school-student-dt-pagination',
        columns: ['student_name','admission_no','campus_name','department_name','class_name','phone_no','para_quantity','status'],
        renderRow: (item) => {
            const statusBadge = item.status
                ? '<span class="badge badge-success">{{ bi('Active') }}</span>'
                : '<span class="badge badge-default">{{ bi('Inactive') }}</span>';
            const esBadgeMap = {
                enrolled:  '<span class="badge badge-success">{{ bi('Enrolled') }}</span>',
                left_self: '<span class="badge badge-warning">{{ bi('Left (Self)') }}</span>',
                expelled:  '<span class="badge badge-danger">{{ bi('Expelled') }}</span>',
                completed: '<span class="badge badge-primary">{{ bi('Completed') }}</span>',
            };
            const enrollBadge = esBadgeMap[item.enrollment_status] || '<span class="badge badge-default">' + item.enrollment_status + '</span>';
            const paraValue = (item.para_quantity !== null && item.para_quantity !== undefined) ? item.para_quantity : '-';
            const paraCell = @can('school_student.edit')
                (() => {
                    const detail = JSON.stringify({ id: item.id, name: item.student_name, value: item.para_quantity ?? null }).replace(/'/g, '&#39;');
                    return `<button type="button" class="btn btn-default btn-xs" title="{{ bi('Update Para Quantity') }}" onclick='window.dispatchEvent(new CustomEvent("open-para-quantity-modal", { detail: ${detail} }))'>${paraValue} <iconify-icon icon="lucide:pencil" width="12"></iconify-icon></button>`;
                })();
            @else
                `${paraValue}`;
            @endcan
            const actions = SchoolDatatable.actions({
                @can('school_student.view')
                viewUrl: `{{ url('admin/school/students') }}/${item.id}`,
                @endcan
                @can('school_student.edit')
                canEdit: true,
                editUrl: `{{ url('admin/school/students') }}/${item.id}/edit`,
                @endcan
                @can('school_student.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/school/students') }}/${item.id}`,
                deleteConfirm: '{{ bi('Delete this student?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium">${item.student_name}</td>
                <td class="px-4 py-3 text-gray-500">${item.admission_no}</td>
                <td class="px-4 py-3">${item.campus_name}</td>
                <td class="px-4 py-3">${item.department_name}</td>
                <td class="px-4 py-3">${item.class_name}</td>
                <td class="px-4 py-3">${item.phone_no}</td>
                <td class="px-4 py-3">${paraCell}</td>
                <td class="px-4 py-3">${enrollBadge}</td>
                <td class="px-4 py-3">${statusBadge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__currentDt = studentDt;
    studentDt.bindSearch('dt-search');
    studentDt.bindFilter('dt-campus', 'campus');
    studentDt.bindFilter('dt-department', 'department');
    studentDt.bindFilter('dt-class', 'class');
    studentDt.bindFilter('dt-status', 'status');
    studentDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
