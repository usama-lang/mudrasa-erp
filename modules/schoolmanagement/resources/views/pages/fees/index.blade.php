<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-green-100 dark:bg-green-900/30 p-3">
                    <iconify-icon icon="lucide:banknote" class="h-6 w-6 text-green-600 dark:text-green-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('This Month Collection') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">PKR {{ number_format($stats['this_month_collection'], 0) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-red-100 dark:bg-red-900/30 p-3">
                    <iconify-icon icon="lucide:alert-circle" class="h-6 w-6 text-red-600 dark:text-red-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Pending Monthly Dues') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_dues'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="rounded-lg bg-blue-100 dark:bg-blue-900/30 p-3">
                    <iconify-icon icon="lucide:receipt" class="h-6 w-6 text-blue-600 dark:text-blue-400" width="24" height="24"></iconify-icon>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ bi('Total Payments') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_payments'] }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        {{-- Filter bar --}}
        <form method="GET" action="{{ route('school.fees.index') }}" class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mb-4">
            <div class="flex items-end gap-2 flex-wrap">
                @if($isSuperAdmin)
                <div>
                    <label class="form-label">{{ bi('Campus') }}</label>
                    <select name="campus" class="form-control text-sm">
                        <option value="">{{ bi('All Campuses') }}</option>
                        @foreach($campuses as $c)
                        <option value="{{ $c->id }}" @selected(request('campus') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="form-label">{{ bi('Fee Month') }}</label>
                    <input type="month" name="month" value="{{ request('month') }}" class="form-control text-sm">
                </div>
                <div>
                    <label class="form-label">{{ bi('Status') }}</label>
                    <select name="status" class="form-control text-sm">
                        <option value="">{{ bi('All Status') }}</option>
                        <option value="paid" @selected(request('status') === 'paid')>{{ bi('Paid') }}</option>
                        <option value="partial" @selected(request('status') === 'partial')>{{ bi('Partial') }}</option>
                        <option value="waived" @selected(request('status') === 'waived')>{{ bi('Waived') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-default">{{ bi('Apply') }}</button>
            </div>
            @can('school_fee.collect')
            <a href="{{ route('school.fees.create') }}" wire:navigate class="btn btn-primary">
                <iconify-icon icon="lucide:plus"></iconify-icon> {{ bi('Collect Fee') }}
            </a>
            @endcan
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">{{ bi('Receipt No.') }}</th>
                        <th class="px-4 py-3">{{ bi('Student') }}</th>
                        <th class="px-4 py-3">{{ bi('Class') }}</th>
                        <th class="px-4 py-3">{{ bi('Month') }}</th>
                        <th class="px-4 py-3">{{ bi('Amount') }}</th>
                        <th class="px-4 py-3">{{ bi('Status') }}</th>
                        <th class="px-4 py-3">{{ bi('Date') }}</th>
                        <th class="px-4 py-3">{{ bi('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    @php
                        $statusClass = match($payment->status) {
                            'paid'    => 'badge-success',
                            'partial' => 'badge-warning',
                            'waived'  => 'badge-default',
                            default   => 'badge-default',
                        };
                        $statusLabel = match($payment->status) {
                            'paid'    => bi('Paid'),
                            'partial' => bi('Partial'),
                            'waived'  => bi('Waived'),
                            default   => $payment->status,
                        };
                    @endphp
                    <tr wire:key="fee-{{ $payment->id }}" class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-gray-500">{{ $payment->receipt_no }}</td>
                        <td class="px-4 py-3 font-medium">{{ $payment->student?->student_name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $payment->schoolClass?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $payment->fee_month->format('F Y') }}</td>
                        <td class="px-4 py-3">PKR {{ number_format((float) $payment->amount_paid, 0) }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                        <td class="px-4 py-3 text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('school.fees.show', $payment->id) }}" wire:navigate class="text-blue-600 hover:underline dark:text-blue-400">{{ bi('View') }}</a>
                                <a href="{{ route('school.fees.show', ['feePayment' => $payment->id, 'print' => 1]) }}" target="_blank" class="text-gray-600 hover:underline dark:text-gray-400">{{ bi('Print') }}</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-8 text-gray-500">{{ bi('No payments found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </x-card>

</x-layouts.backend-layout>
