<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ mf_bi('This Month Collection') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">PKR {{ number_format($thisMonth, 0) }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ mf_bi('Total Collection') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">PKR {{ number_format($totalCollection, 0) }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ mf_bi('Funds') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $fundTotals->count() }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card>
            <x-slot name="title">{{ mf_bi('Fund Totals') }}</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">{{ mf_bi('Fund') }}</th>
                            <th class="px-4 py-3 text-right">{{ mf_bi('Total Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fundTotals as $fund)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $fund->name }}
                                    @if($fund->name_urdu)<span class="text-gray-400 text-xs">/ {{ $fund->name_urdu }}</span>@endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">PKR {{ number_format((float) $fund->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center py-8 text-gray-500">{{ mf_bi('No records found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <x-slot name="title">{{ mf_bi('Recent Receipts') }}</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">{{ mf_bi('Receipt No.') }}</th>
                            <th class="px-4 py-3">{{ mf_bi('Fund') }}</th>
                            <th class="px-4 py-3 text-right">{{ mf_bi('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReceipts as $receipt)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">
                                    <a href="{{ route('admin.madrasafunds.receipts.show', $receipt) }}" class="text-primary hover:underline">{{ $receipt->receipt_number }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $receipt->fund?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">PKR {{ number_format((float) $receipt->amount, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-8 text-gray-500">{{ mf_bi('No records found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

</x-layouts.backend-layout>
