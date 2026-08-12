<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('schoolmanagement::partials.school-datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ bi('Search campuses...') }}">
            <div class="flex items-center gap-2">
                <select id="dt-status" class="form-control text-sm">
                    <option value="">{{ bi('All Status') }}</option>
                    <option value="active">{{ bi('Active') }}</option>
                    <option value="inactive">{{ bi('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="school-campus-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ bi('Name') }}</th>
                        <th class="px-4 py-3" data-sort="code">{{ bi('Code') }}</th>
                        <th class="px-4 py-3">{{ bi('Departments') }}</th>
                        <th class="px-4 py-3">{{ bi('Students') }}</th>
                        <th class="px-4 py-3" data-sort="status">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="school-campus-dt-body">
                    <tr><td colspan="6" class="text-center py-8 text-gray-500">{{ bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="school-campus-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const campusDt = new SchoolDatatable({
        url: '{{ route('school.campuses.data') }}',
        tableId: 'school-campus-dt',
        bodyId: 'school-campus-dt-body',
        paginationId: 'school-campus-dt-pagination',
        columns: ['name','code','departments_count','students_count','status'],
        renderRow: (item) => {
            const badge = item.status === 'active'
                ? '<span class="badge badge-success">{{ bi('Active') }}</span>'
                : '<span class="badge badge-default">{{ bi('Inactive') }}</span>';
            const actions = SchoolDatatable.actions({
                @can('school_campus.view')
                viewUrl: `{{ url('admin/school/campuses') }}/${item.id}`,
                @endcan
                @can('school_campus.edit')
                canEdit: true,
                editUrl: `{{ url('admin/school/campuses') }}/${item.id}/edit`,
                @endcan
                @can('school_campus.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/school/campuses') }}/${item.id}`,
                deleteConfirm: '{{ bi('Delete this campus?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium">${item.name}</td>
                <td class="px-4 py-3 text-gray-500">${item.code ?? '-'}</td>
                <td class="px-4 py-3">${item.departments_count ?? 0}</td>
                <td class="px-4 py-3">${item.students_count ?? 0}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__currentDt = campusDt;
    campusDt.bindSearch('dt-search');
    campusDt.bindFilter('dt-status', 'status');
    campusDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
