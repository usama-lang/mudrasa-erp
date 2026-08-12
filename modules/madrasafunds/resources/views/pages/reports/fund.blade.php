<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <x-card>
        @include('madrasafunds::pages.reports.partials.filters', [
            'action' => route('admin.madrasafunds.reports.fund'),
            'show'   => ['fund_type', 'dates'],
        ])

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">{{ mf_bi('Fund') }}</th>
                        <th class="px-4 py-3 text-right">{{ mf_bi('Total Receipts') }}</th>
                        <th class="px-4 py-3 text-right">{{ mf_bi('Total Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row->fund?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ number_format((int) $row->receipts) }}</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">PKR {{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-8 text-gray-500">{{ mf_bi('No records found.') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-700/40 font-semibold">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ mf_bi('Grand Total') }}</td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ number_format($summary['count']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white">PKR {{ number_format($summary['total'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>

</x-layouts.backend-layout>
