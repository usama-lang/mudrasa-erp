<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('madrasafunds::partials.datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ mf_bi('Search students...') }}">
            <div class="flex items-center gap-2">
                <select id="dt-department" class="form-control text-sm">
                    <option value="">{{ mf_bi('All Departments') }}</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <select id="dt-status" class="form-control text-sm">
                    <option value="">{{ mf_bi('All Status') }}</option>
                    <option value="active">{{ mf_bi('Active') }}</option>
                    <option value="inactive">{{ mf_bi('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="mf-students-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ mf_bi('Name') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Father Name') }}</th>
                        <th class="px-4 py-3" data-sort="class">{{ mf_bi('Class') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Department') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Status') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="mf-students-dt-body">
                    <tr><td colspan="6" class="text-center py-8 text-gray-500">{{ mf_bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="mf-students-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const mfStudentsDt = new MfDatatable({
        url: '{{ route('admin.madrasafunds.students.data') }}',
        tableId: 'mf-students-dt',
        bodyId: 'mf-students-dt-body',
        paginationId: 'mf-students-dt-pagination',
        columns: ['name','father_name','class','department_name','status'],
        renderRow: (item) => {
            const badge = MfDatatable.badge(item.status === 'active', item.status === 'active' ? '{{ mf_bi('Active') }}' : '{{ mf_bi('Inactive') }}');
            const actions = MfDatatable.actions({
                @can('madrasa_funds.edit')
                canEdit: true,
                editUrl: `{{ url('admin/madrasafunds/students') }}/${item.id}/edit`,
                @endcan
                @can('madrasa_funds.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/madrasafunds/students') }}/${item.id}`,
                deleteConfirm: '{{ mf_bi('Delete this student?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${item.name}</td>
                <td class="px-4 py-3 text-gray-500">${item.father_name}</td>
                <td class="px-4 py-3 text-gray-500">${item.class}</td>
                <td class="px-4 py-3 text-gray-500">${item.department_name}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__mfDt = mfStudentsDt;
    mfStudentsDt.bindSearch('dt-search');
    mfStudentsDt.bindFilter('dt-department', 'department');
    mfStudentsDt.bindFilter('dt-status', 'status');
    mfStudentsDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
