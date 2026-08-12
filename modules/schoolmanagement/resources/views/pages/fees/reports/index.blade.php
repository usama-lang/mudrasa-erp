<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @push('styles')
    <style>
        @media print { .sidebar, header, .no-print, nav, aside { display: none !important; } }
    </style>
    @endpush

    {{-- Filters --}}
    <x-card class="mb-6 no-print">
        <x-slot name="title">{{ bi('Report Filters') }}</x-slot>
        <form method="GET" action="{{ route('school.fees.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @if($isSuperAdmin)
            <div>
                <label class="form-label">{{ bi('Campus') }}</label>
                <select name="campus_id" class="form-control">
                    <option value="">{{ bi('All Campuses') }}</option>
                    @foreach($campuses as $c)
                    <option value="{{ $c->id }}" @selected(($filters['campus_id'] ?? null) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="form-label">{{ bi('Department') }}</label>
                <select name="department_id" class="form-control">
                    <option value="">{{ bi('All Departments') }}</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}" @selected(($filters['department_id'] ?? null) == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">{{ bi('Class') }}</label>
                <select name="class_id" class="form-control">
                    <option value="">{{ bi('All Classes') }}</option>
                    @foreach($classes as $cl)
                    <option value="{{ $cl->id }}" @selected(($filters['class_id'] ?? null) == $cl->id)>{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">{{ bi('Status') }}</label>
                <select name="status" class="form-control">
                    <option value="">{{ bi('All Status') }}</option>
                    <option value="paid" @selected(($filters['status'] ?? null) === 'paid')>{{ bi('Paid') }}</option>
                    <option value="partial" @selected(($filters['status'] ?? null) === 'partial')>{{ bi('Partial') }}</option>
                    <option value="waived" @selected(($filters['status'] ?? null) === 'waived')>{{ bi('Waived') }}</option>
                </select>
            </div>
            <div>
                <label class="form-label">{{ bi('Month From') }}</label>
                <input type="month" name="month_from" value="{{ $filters['month_from'] ?? '' }}" class="form-control">
            </div>
            <div>
                <label class="form-label">{{ bi('Month To') }}</label>
                <input type="month" name="month_to" value="{{ $filters['month_to'] ?? '' }}" class="form-control">
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                <button type="submit" class="btn btn-primary">{{ bi('Generate Report') }}</button>
                <button type="button" onclick="window.print()" class="btn btn-default">{{ bi('Print') }}</button>
                <a href="{{ route('school.fees.reports.department') }}" wire:navigate class="btn btn-default">{{ bi('Department Summary') }}</a>
                <a href="{{ route('school.fees.reports.class') }}" wire:navigate class="btn btn-default">{{ bi('Class Summary') }}</a>
            </div>
        </form>
    </x-card>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        <x-card><p class="text-xs text-gray-500">{{ bi('Total Due') }}</p><p class="text-lg font-bold text-gray-900 dark:text-white">PKR {{ number_format((float) $summary['total_due'], 0) }}</p></x-card>
        <x-card><p class="text-xs text-gray-500">{{ bi('Total Paid') }}</p><p class="text-lg font-bold text-green-600">PKR {{ number_format((float) $summary['total_paid'], 0) }}</p></x-card>
        <x-card><p class="text-xs text-gray-500">{{ bi('Total Discount') }}</p><p class="text-lg font-bold text-gray-900 dark:text-white">PKR {{ number_format((float) $summary['total_discount'], 0) }}</p></x-card>
        <x-card><p class="text-xs text-gray-500">{{ bi('Total Balance') }}</p><p class="text-lg font-bold text-red-600">PKR {{ number_format((float) $summary['total_balance'], 0) }}</p></x-card>
        <x-card><p class="text-xs text-gray-500">{{ bi('Count') }}</p><p class="text-lg font-bold text-gray-900 dark:text-white">{{ $summary['count'] }}</p></x-card>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-3">{{ bi('Receipt No.') }}</th>
                        <th class="px-3 py-3">{{ bi('Student') }}</th>
                        <th class="px-3 py-3">{{ bi('Class') }}</th>
                        <th class="px-3 py-3">{{ bi('Month') }}</th>
                        <th class="px-3 py-3">{{ bi('Total Due') }}</th>
                        <th class="px-3 py-3">{{ bi('Paid') }}</th>
                        <th class="px-3 py-3">{{ bi('Discount') }}</th>
                        <th class="px-3 py-3">{{ bi('Balance') }}</th>
                        <th class="px-3 py-3">{{ bi('Status') }}</th>
                        <th class="px-3 py-3">{{ bi('Date') }}</th>
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
                    @endphp
                    <tr wire:key="report-{{ $payment->id }}" class="border-b border-gray-100 dark:border-gray-700">
                        <td class="px-3 py-2 font-mono text-gray-500">{{ $payment->receipt_no }}</td>
                        <td class="px-3 py-2 font-medium">{{ $payment->student?->student_name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $payment->schoolClass?->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $payment->fee_month->format('M Y') }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $payment->total_due, 0) }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $payment->amount_paid, 0) }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $payment->discount, 0) }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $payment->balance, 0) }}</td>
                        <td class="px-3 py-2"><span class="badge {{ $statusClass }}">{{ $payment->status }}</span></td>
                        <td class="px-3 py-2 text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-8 text-gray-500">{{ bi('No payments found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 no-print">
            {{ $payments->links() }}
        </div>
    </x-card>

</x-layouts.backend-layout>
