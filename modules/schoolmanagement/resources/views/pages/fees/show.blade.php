<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @push('styles')
    <style>
        @media print {
            .sidebar, header, .no-print, nav, aside { display: none !important; }
            body, .main-content, main { background: #fff !important; margin: 0 !important; padding: 0 !important; }
            #fee-receipt { box-shadow: none !important; border: 1px solid #000 !important; max-width: 100% !important; }
        }
    </style>
    @endpush

    @php
        $food = (float) $payment->food_charges;
        $methodLabel = match($payment->payment_method) {
            'cash'          => bi('Cash'),
            'bank_transfer' => bi('Bank Transfer'),
            'cheque'        => bi('Cheque'),
            default         => $payment->payment_method,
        };
    @endphp

    <div class="flex justify-end gap-2 mb-4 no-print">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            <iconify-icon icon="lucide:printer"></iconify-icon> {{ bi('Print Receipt') }}
        </button>
        <a href="{{ route('school.fees.index') }}" wire:navigate class="btn btn-default">{{ bi('Back to List') }}</a>
    </div>

    <div id="fee-receipt" class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Header --}}
        <div class="text-center px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ bi('Fee Receipt') }}</p>
        </div>

        {{-- Meta --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between text-sm">
            <div>
                <span class="text-gray-500">{{ bi('Receipt No.') }}:</span>
                <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $payment->receipt_no }}</span>
            </div>
            <div>
                <span class="text-gray-500">{{ bi('Date') }}:</span>
                <span class="text-gray-900 dark:text-white">{{ $payment->payment_date->format('F d, Y') }}</span>
            </div>
        </div>

        {{-- Student --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3 text-sm">
            <div><span class="text-gray-500">{{ bi('Student') }}:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $payment->student?->student_name ?? '—' }}</span></div>
            <div><span class="text-gray-500">{{ bi('Admission No.') }}:</span> <span class="font-mono text-gray-900 dark:text-white">{{ $payment->student?->admission_no ?? '—' }}</span></div>
            <div><span class="text-gray-500">{{ bi('Class') }}:</span> <span class="text-gray-900 dark:text-white">{{ $payment->schoolClass?->name ?? '—' }}{{ $payment->section ? ' - ' . $payment->section->name : '' }}</span></div>
            <div><span class="text-gray-500">{{ bi('Campus') }}:</span> <span class="text-gray-900 dark:text-white">{{ $payment->campus?->name ?? '—' }}</span></div>
        </div>

        {{-- Breakdown --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-sm">
            <div class="flex justify-between py-1">
                <span class="text-gray-600 dark:text-gray-300">{{ bi('Fee Month') }}</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->fee_month->format('F Y') }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-gray-600 dark:text-gray-300">{{ bi('Monthly Fee') }}</span>
                <span class="text-gray-900 dark:text-white">PKR {{ number_format((float) $payment->monthly_fee, 0) }}</span>
            </div>
            @if($food > 0)
            <div class="flex justify-between py-1">
                <span class="text-gray-600 dark:text-gray-300">{{ bi('Food Charges') }}</span>
                <span class="text-gray-900 dark:text-white">PKR {{ number_format($food, 0) }}</span>
            </div>
            @endif
            <div class="flex justify-between py-1">
                <span class="text-gray-600 dark:text-gray-300">{{ bi('Discount') }}</span>
                <span class="text-gray-900 dark:text-white">PKR {{ number_format((float) $payment->discount, 0) }}</span>
            </div>
            <div class="border-t border-dashed border-gray-300 dark:border-gray-600 my-2"></div>
            <div class="flex justify-between py-1 font-semibold">
                <span class="text-gray-900 dark:text-white">{{ bi('Total Paid') }}</span>
                <span class="text-gray-900 dark:text-white">PKR {{ number_format((float) $payment->amount_paid, 0) }}</span>
            </div>
            <div class="flex justify-between py-1 font-semibold">
                <span class="text-gray-900 dark:text-white">{{ bi('Balance') }}</span>
                <span class="{{ (float) $payment->balance > 0 ? 'text-red-600' : 'text-green-600' }}">PKR {{ number_format((float) $payment->balance, 0) }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-gray-600 dark:text-gray-300">{{ bi('Payment Method') }}</span>
                <span class="text-gray-900 dark:text-white">{{ $methodLabel }}</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-5 flex justify-between items-end text-sm">
            <div>
                <span class="text-gray-500">{{ bi('Collected By') }}:</span>
                <span class="text-gray-900 dark:text-white">{{ $payment->collectedBy?->name ?? $payment->collectedBy?->first_name ?? '—' }}</span>
            </div>
            <div class="text-right">
                <div class="border-t border-gray-400 dark:border-gray-500 w-40 pt-1 mt-6"></div>
                <span class="text-gray-500">{{ bi('Signature') }}</span>
            </div>
        </div>

        @if($payment->notes)
        <div class="px-6 pb-4 text-xs text-gray-500">{{ $payment->notes }}</div>
        @endif
    </div>

    @if($autoPrint)
    @push('scripts')
    <script>
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 300); });
    </script>
    @endpush
    @endif

</x-layouts.backend-layout>
