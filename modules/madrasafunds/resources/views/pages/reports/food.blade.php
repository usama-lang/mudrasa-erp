<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <x-card>
        @include('madrasafunds::pages.reports.partials.filters', [
            'action' => route('admin.madrasafunds.reports.food'),
            'show'   => ['department', 'dates'],
        ])

        <div class="flex flex-wrap gap-6 mb-4 text-sm">
            <div><span class="text-gray-500">{{ mf_bi('Total Receipts') }}:</span> <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($summary['count']) }}</span></div>
            <div><span class="text-gray-500">{{ mf_bi('Total Amount') }}:</span> <span class="font-semibold text-gray-900 dark:text-white">PKR {{ number_format($summary['total'], 2) }}</span></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">{{ mf_bi('Receipt No.') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Date') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Student') }}</th>
                        <th class="px-4 py-3">{{ mf_bi('Department') }}</th>
                        <th class="px-4 py-3 text-right">{{ mf_bi('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">
                                <a href="{{ route('admin.madrasafunds.receipts.show', $receipt) }}" class="text-primary hover:underline">{{ $receipt->receipt_number }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $receipt->receipt_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $receipt->student?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $receipt->student?->department?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">PKR {{ number_format((float) $receipt->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ mf_bi('No records found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $receipts->links() }}</div>
    </x-card>

</x-layouts.backend-layout>
