<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('schoolmanagement::partials.school-datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ bi('Search teachers...') }}">
            <div class="flex items-center gap-2">
                @if(auth()->user()->hasRole(['superadmin','admin']))
                <select id="dt-campus" class="form-control text-sm">
                    <option value="">{{ bi('All Campuses') }}</option>
                    @foreach(\Modules\SchoolManagement\Models\Campus::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @endif
                <select id="dt-status" class="form-control text-sm">
                    <option value="">{{ bi('All Status') }}</option>
                    <option value="active">{{ bi('Active') }}</option>
                    <option value="inactive">{{ bi('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="school-teacher-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">{{ bi('Name') }}</th>
                        <th class="px-4 py-3" data-sort="employee_id">{{ bi('Employee ID') }}</th>
                        <th class="px-4 py-3">{{ bi('Campus') }}</th>
                        <th class="px-4 py-3" data-sort="designation">{{ bi('Designation') }}</th>
                        <th class="px-4 py-3" data-sort="joining_date">{{ bi('Joining Date') }}</th>
                        <th class="px-4 py-3" data-sort="status">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="school-teacher-dt-body">
                    <tr><td colspan="7" class="text-center py-8 text-gray-500">{{ bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="school-teacher-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const teacherDt = new SchoolDatatable({
        url: '{{ route('school.teachers.data') }}',
        tableId: 'school-teacher-dt',
        bodyId: 'school-teacher-dt-body',
        paginationId: 'school-teacher-dt-pagination',
        columns: ['full_name','employee_id','campus_name','designation','joining_date','status'],
        renderRow: (item) => {
            const badge = item.status === 'active'
                ? '<span class="badge badge-success">{{ bi('Active') }}</span>'
                : '<span class="badge badge-default">{{ bi('Inactive') }}</span>';
            const actions = SchoolDatatable.actions({
                @can('school_teacher.view')
                viewUrl: `{{ url('admin/school/teachers') }}/${item.id}`,
                @endcan
                @can('school_teacher.edit')
                canEdit: true,
                editUrl: `{{ url('admin/school/teachers') }}/${item.id}/edit`,
                @endcan
                @can('school_teacher.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/school/teachers') }}/${item.id}`,
                deleteConfirm: '{{ bi('Delete this teacher?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium">${item.full_name}</td>
                <td class="px-4 py-3 text-gray-500">${item.employee_id}</td>
                <td class="px-4 py-3">${item.campus_name}</td>
                <td class="px-4 py-3">${item.designation}</td>
                <td class="px-4 py-3">${item.joining_date}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__currentDt = teacherDt;
    teacherDt.bindSearch('dt-search');
    teacherDt.bindFilter('dt-campus', 'campus');
    teacherDt.bindFilter('dt-status', 'status');
    teacherDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
