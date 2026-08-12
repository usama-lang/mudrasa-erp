<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('schoolmanagement::partials.school-datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ bi('Search classes...') }}">
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
                <select id="dt-status" class="form-control text-sm">
                    <option value="">{{ bi('All Status') }}</option>
                    <option value="active">{{ bi('Active') }}</option>
                    <option value="inactive">{{ bi('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="school-class-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ bi('Name') }}</th>
                        <th class="px-4 py-3">{{ bi('Campus') }}</th>
                        <th class="px-4 py-3">{{ bi('Department') }}</th>
                        <th class="px-4 py-3" data-sort="status">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="school-class-dt-body">
                    <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="school-class-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const classDt = new SchoolDatatable({
        url: '{{ route('school.classes.data') }}',
        tableId: 'school-class-dt',
        bodyId: 'school-class-dt-body',
        paginationId: 'school-class-dt-pagination',
        columns: ['name','campus_name','department_name','status'],
        renderRow: (item) => {
            const badge = item.status === 'active'
                ? '<span class="badge badge-success">{{ bi('Active') }}</span>'
                : '<span class="badge badge-default">{{ bi('Inactive') }}</span>';
            const actions = SchoolDatatable.actions({
                @can('school_class.view')
                viewUrl: `{{ url('admin/school/classes') }}/${item.id}`,
                @endcan
                @can('school_class.edit')
                canEdit: true,
                editUrl: `{{ url('admin/school/classes') }}/${item.id}/edit`,
                @endcan
                @can('school_class.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/school/classes') }}/${item.id}`,
                deleteConfirm: '{{ bi('Delete this class?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium">${item.name}</td>
                <td class="px-4 py-3">${item.campus_name}</td>
                <td class="px-4 py-3">${item.department_name}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__currentDt = classDt;
    classDt.bindSearch('dt-search');
    classDt.bindFilter('dt-campus', 'campus');
    classDt.bindFilter('dt-department', 'department');
    classDt.bindFilter('dt-status', 'status');
    classDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
