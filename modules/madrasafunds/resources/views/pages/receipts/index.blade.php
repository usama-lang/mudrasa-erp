<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @include('madrasafunds::partials.datatable-script')

    <x-card>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
            <input id="dt-search" type="search" class="form-control" placeholder="{{ mf_bi('Search receipts...') }}">
            <select id="dt-fund" class="form-control text-sm">
                <option value="">{{ mf_bi('-- Select Fund --') }}</option>
                @foreach($funds as $fund)
                    <option value="{{ $fund->id }}">{{ $fund->name }}</option>
                @endforeach
            </select>
            <select id="dt-department" class="form-control text-sm">
                <option value="">{{ mf_bi('All Departments') }}</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
            <input id="dt-date-from" type="date" class="form-control text-sm" aria-label="{{ mf_bi('Date From') }}">
            <input id="dt-date-to" type="date" class="form-control text-sm" aria-label="{{ mf_bi('Date To') }}">
        </div>

        <div class="overflow-x-auto">
            <table id="mf-receipts-dt" class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" data-sort="receipt_number">{{ mf_bi('Receipt No.') }}</th>
                        <th class="px-4 py-3" data-sort="receipt_date">{{ mf_bi('Date') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Fund') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Department') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Donor Name') }}</th>
                        <th class="px-4 py-3 text-right" data-sort="amount">{{ mf_bi('Amount') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="mf-receipts-dt-body">
                    <tr><td colspan="7" class="text-center py-8 text-gray-500">{{ mf_bi('Loading...') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div id="mf-receipts-dt-pagination" class="mt-4"></div>
    </x-card>

    @push('scripts')
    <script>
    const mfReceiptsDt = new MfDatatable({
        url: '{{ route('admin.madrasafunds.receipts.data') }}',
        tableId: 'mf-receipts-dt',
        bodyId: 'mf-receipts-dt-body',
        paginationId: 'mf-receipts-dt-pagination',
        columns: ['receipt_number','receipt_date','fund_name','department_name','payer','amount'],
        renderRow: (item) => {
            const cancelled = item.is_cancelled
                ? ' <span class="badge badge-danger">{{ mf_bi('Cancelled') }}</span>'
                : '';
            let html = '<div class="flex items-center gap-1">';
            @can('madrasa_funds.print_receipt')
            html += `<a href="{{ url('admin/madrasafunds/receipts') }}/${item.id}/print" class="btn btn-default btn-xs" title="{{ mf_bi('Print') }}"><iconify-icon icon="lucide:printer" width="14"></iconify-icon></a>`;
            @endcan
            html += `<a href="{{ url('admin/madrasafunds/receipts') }}/${item.id}" class="btn btn-default btn-xs" title="{{ mf_bi('View') }}"><iconify-icon icon="lucide:eye" width="14"></iconify-icon></a>`;
            @can('madrasa_funds.edit')
            html += `<a href="{{ url('admin/madrasafunds/receipts') }}/${item.id}/edit" class="btn btn-default btn-xs" title="{{ mf_bi('Edit') }}"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></a>`;
            @endcan
            @can('madrasa_funds.delete')
            html += `<form method="POST" action="{{ url('admin/madrasafunds/receipts') }}/${item.id}" onsubmit="return confirm('{{ mf_bi('Delete this receipt?') }}')">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-danger btn-xs" title="{{ mf_bi('Delete') }}"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </form>`;
            @endcan
            html += '</div>';

            return `<tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 ${item.is_cancelled ? 'opacity-60' : ''}">
                <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">${item.receipt_number}${cancelled}</td>
                <td class="px-4 py-3 text-gray-500">${item.receipt_date ?? '-'}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">${item.fund_name}</td>
                <td class="px-4 py-3 text-gray-500">${item.department_name}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">${item.payer}</td>
                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">${item.amount}</td>
                <td class="px-4 py-3">${html}</td>
            </tr>`;
        },
    });
    window.__mfDt = mfReceiptsDt;
    mfReceiptsDt.bindSearch('dt-search');
    mfReceiptsDt.bindFilter('dt-fund', 'fund');
    mfReceiptsDt.bindFilter('dt-department', 'department');
    mfReceiptsDt.bindInput('dt-date-from', 'date_from');
    mfReceiptsDt.bindInput('dt-date-to', 'date_to');
    mfReceiptsDt.init();
    </script>
    @endpush

</x-layouts.backend-layout>
