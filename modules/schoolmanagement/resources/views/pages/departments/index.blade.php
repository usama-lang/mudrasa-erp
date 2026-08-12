<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('schoolmanagement::partials.school-datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ bi('Search departments...') }}">
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
            <table id="school-dept-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ bi('Name') }}</th>
                        <th class="px-4 py-3" data-sort="code">{{ bi('Code') }}</th>
                        <th class="px-4 py-3">{{ bi('Campus') }}</th>
                        <th class="px-4 py-3" data-sort="status">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="school-dept-dt-body">
                    <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="school-dept-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const deptDt = new SchoolDatatable({
        url: '{{ route('school.departments.data') }}',
        tableId: 'school-dept-dt',
        bodyId: 'school-dept-dt-body',
        paginationId: 'school-dept-dt-pagination',
        columns: ['name','code','campus_name','status'],
        renderRow: (item) => {
            const badge = item.status === 'active'
                ? '<span class="badge badge-success">{{ bi('Active') }}</span>'
                : '<span class="badge badge-default">{{ bi('Inactive') }}</span>';
            const actions = SchoolDatatable.actions({
                @can('school_department.view')
                viewUrl: `{{ url('admin/school/departments') }}/${item.id}`,
                @endcan
                @can('school_department.edit')
                canEdit: true,
                editUrl: `{{ url('admin/school/departments') }}/${item.id}/edit`,
                @endcan
                @can('school_department.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/school/departments') }}/${item.id}`,
                deleteConfirm: '{{ bi('Delete this department?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium">${item.name}</td>
                <td class="px-4 py-3 text-gray-500">${item.code ?? '-'}</td>
                <td class="px-4 py-3">${item.campus_name}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__currentDt = deptDt;
    deptDt.bindSearch('dt-search');
    deptDt.bindFilter('dt-campus', 'campus');
    deptDt.bindFilter('dt-status', 'status');
    deptDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
