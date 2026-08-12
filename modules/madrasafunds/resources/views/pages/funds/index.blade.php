<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('madrasafunds::partials.datatable-script')

    <x-card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control w-full sm:w-72" placeholder="{{ mf_bi('Search funds...') }}">
            <div class="flex items-center gap-2">
                <select id="dt-type" class="form-control text-sm">
                    <option value="">{{ mf_bi('Fund Type') }}</option>
                    @foreach(\Modules\MadrasaFunds\Enums\FundType::options() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
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
            <table id="mf-funds-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="name">{{ mf_bi('Name') }}</th>
                        <th class="px-4 py-3" data-sort="type">{{ mf_bi('Type') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Status') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="mf-funds-dt-body">
                    <tr><td colspan="4" class="text-center py-8 text-gray-500">{{ mf_bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="mf-funds-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const mfFundsDt = new MfDatatable({
        url: '{{ route('admin.madrasafunds.funds.data') }}',
        tableId: 'mf-funds-dt',
        bodyId: 'mf-funds-dt-body',
        paginationId: 'mf-funds-dt-pagination',
        columns: ['name','type','status'],
        renderRow: (item) => {
            const badge = MfDatatable.badge(item.status === 'active', item.status === 'active' ? '{{ mf_bi('Active') }}' : '{{ mf_bi('Inactive') }}');
            const name = item.name + (item.name_urdu ? ` <span class="text-gray-400 text-xs">/ ${item.name_urdu}</span>` : '');
            const actions = MfDatatable.actions({
                @can('madrasa_funds.manage_funds')
                canEdit: true,
                editUrl: `{{ url('admin/madrasafunds/funds') }}/${item.id}/edit`,
                canDelete: true,
                deleteUrl: `{{ url('admin/madrasafunds/funds') }}/${item.id}`,
                deleteConfirm: '{{ mf_bi('Delete this fund?') }}',
                @endcan
            });
            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${name}</td>
                <td class="px-4 py-3 text-gray-500">${item.type}</td>
                <td class="px-4 py-3">${badge}</td>
                <td class="px-4 py-3">${actions}</td>
            </tr>`;
        },
    });
    window.__mfDt = mfFundsDt;
    mfFundsDt.bindSearch('dt-search');
    mfFundsDt.bindFilter('dt-type', 'type');
    mfFundsDt.bindFilter('dt-status', 'status');
    mfFundsDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
