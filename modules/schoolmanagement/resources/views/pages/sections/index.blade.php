<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('schoolmanagement::partials.school-datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ bi('Search sections...') }}">
            <div class="flex items-center gap-2 flex-wrap">
                @if(auth()->user()->hasRole(['superadmin','admin']))
                <select id="dt-campus" class="form-control text-sm">
                    <option value="">{{ bi('All Campuses') }}</option>
                    @foreach(\Modules\SchoolManagement\Models\Campus::orderBy('name')->get() as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @endif
                <select id="dt-class" class="form-control text-sm">
                    <option value="">{{ bi('All Classes') }}</option>
                    @foreach(\Modules\SchoolManagement\Models\SchoolClass::orderBy('name')->get() as $cl)
                    <option value="{{ $cl->id }}">{{ $cl->name }}</option>
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
            <table id="school-section-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ bi('Name') }}</th>
                        <th class="px-4 py-3">{{ bi('Campus') }}</th>
                        <th class="px-4 py-3">{{ bi('Class') }}</th>
                        <th class="px-4 py-3" data-sort="capacity">{{ bi('Capacity') }}</th>
                        <th class="px-4 py-3" data-sort="status">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="school-section-dt-body">
                    <tr><td colspan="6" class="text-center py-8 text-gray-500">{{ bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="school-section-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const sectionDt = new SchoolDatatable({
        url: '{{ route('school.sections.data') }}',
        tableId: 'school-section-dt',
        bodyId: 'school-section-dt-body',
        paginationId: 'school-section-dt-pagination',
        columns: ['name','campus_name','class_name','capacity','status'],
        renderRow: (item) => {
            const badge = item.status === 'active'
                ? '<span class="badge badge-success">{{ bi('Active') }}</span>'
                : '<span class="badge badge-default">{{ bi('Inactive') }}</span>';
            const actions = SchoolDatatable.actions({
                @can('school_section.view')
                viewUrl: `{{ url('admin/school/sections') }}/${item.id}`,
                @endcan
                @can('school_section.edit')
                canEdit: true,
                editUrl: `{{ url('admin/school/sections') }}/${item.id}/edit`,
                @endcan
                @can('school_section.delete')
                canDelete: true,
                deleteUrl: `{{ url('admin/school/sections') }}/${item.id}`,
                deleteConfirm: '{{ bi('Delete this section?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium">${item.name}</td>
                <td class="px-4 py-3">${item.campus_name}</td>
                <td class="px-4 py-3">${item.class_name}</td>
                <td class="px-4 py-3">${item.capacity ?? '-'}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__currentDt = sectionDt;
    sectionDt.bindSearch('dt-search');
    sectionDt.bindFilter('dt-campus', 'campus');
    sectionDt.bindFilter('dt-class', 'class');
    sectionDt.bindFilter('dt-status', 'status');
    sectionDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
