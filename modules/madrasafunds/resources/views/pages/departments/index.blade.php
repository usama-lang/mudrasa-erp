<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('madrasafunds::partials.datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ mf_bi('Search departments...') }}">
            <select id="dt-status" class="form-control text-sm">
                <option value="">{{ mf_bi('All Status') }}</option>
                <option value="active">{{ mf_bi('Active') }}</option>
                <option value="inactive">{{ mf_bi('Inactive') }}</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table id="mf-dept-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ mf_bi('Name') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Students') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Status') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="mf-dept-dt-body">
                    <tr><td colspan="4" class="text-center py-8 text-gray-500">{{ mf_bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="mf-dept-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const mfDeptDt = new MfDatatable({
        url: '{{ route('admin.madrasafunds.departments.data') }}',
        tableId: 'mf-dept-dt',
        bodyId: 'mf-dept-dt-body',
        paginationId: 'mf-dept-dt-pagination',
        columns: ['name','students_count','status'],
        renderRow: (item) => {
            const badge = MfDatatable.badge(item.status === 'active', item.status === 'active' ? '{{ mf_bi('Active') }}' : '{{ mf_bi('Inactive') }}');
            const name = item.name + (item.name_urdu ? ` <span class="text-gray-400 text-xs">/ ${item.name_urdu}</span>` : '');
            const actions = MfDatatable.actions({
                @can('madrasa_funds.manage_departments')
                canEdit: true,
                editUrl: `{{ url('admin/madrasafunds/departments') }}/${item.id}/edit`,
                canDelete: true,
                deleteUrl: `{{ url('admin/madrasafunds/departments') }}/${item.id}`,
                deleteConfirm: '{{ mf_bi('Delete this department?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${name}</td>
                <td class="px-4 py-3 text-gray-500">${item.students_count}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__mfDt = mfDeptDt;
    mfDeptDt.bindSearch('dt-search');
    mfDeptDt.bindFilter('dt-status', 'status');
    mfDeptDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
