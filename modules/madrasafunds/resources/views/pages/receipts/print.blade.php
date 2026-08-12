<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    @push('styles')
    <style>
        @media print {
            .sidebar, header, .no-print, nav, aside { display: none !important; }
            body, .main-content, main { background: #fff !important; margin: 0 !important; padding: 0 !important; }
            #mf-receipt { box-shadow: none !important; border: 1px solid #000 !important; max-width: 100% !important; }
        }
        #mf-receipt .urdu { font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', serif; }
    </style>
    @endpush

    @php
        $fundTypeLabel = $receipt->fund?->type?->label() ?? '';
        $payer = $receipt->student?->name ?? $receipt->donor_name ?? '—';
    @endphp

    <div class="flex justify-end gap-2 mb-4 no-print">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            <iconify-icon icon="lucide:printer"></iconify-icon> {{ mf_bi('Print Receipt') }}
        </button>
        <a href="{{ route('admin.madrasafunds.receipts.index') }}" class="btn btn-default">{{ mf_bi('Back to List') }}</a>
    </div>

    <div id="mf-receipt" class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Header (bilingual) --}}
        <div class="text-center px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</h1>
            <div class="flex items-center justify-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
                <span>Donation / Fee Receipt</span>
                <span class="urdu" dir="rtl">رسید برائے عطیہ / فیس</span>
            </div>
            @if($receipt->is_cancelled)
                <p class="mt-2 text-sm font-semibold text-red-600">CANCELLED / <span class="urdu" dir="rtl">منسوخ شدہ</span></p>
            @endif
        </div>

        {{-- Meta --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between text-sm">
            <div>
                <span class="text-gray-500">Receipt No. / <span class="urdu" dir="rtl">رسید نمبر</span>:</span>
                <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $receipt->receipt_number }}</span>
            </div>
            <div>
                <span class="text-gray-500">Date / <span class="urdu" dir="rtl">تاریخ</span>:</span>
                <span class="text-gray-900 dark:text-white">{{ $receipt->receipt_date?->format('F d, Y') }}</span>
            </div>
        </div>

        {{-- Body --}}
        <table class="w-full text-sm">
            <tbody>
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-6 py-3 text-gray-500">Fund <span class="urdu" dir="rtl">/ فنڈ</span></td>
                    <td class="px-6 py-3 text-right text-gray-900 dark:text-white">
                        {{ $receipt->fund?->name }}
                        @if($receipt->fund?->name_urdu)<span class="urdu text-gray-500" dir="rtl"> / {{ $receipt->fund->name_urdu }}</span>@endif
                    </td>
                </tr>
                @if($fundTypeLabel)
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-6 py-3 text-gray-500">Type <span class="urdu" dir="rtl">/ قسم</span></td>
                    <td class="px-6 py-3 text-right text-gray-900 dark:text-white">{{ $fundTypeLabel }}</td>
                </tr>
                @endif
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-6 py-3 text-gray-500">
                        {{ $receipt->student ? 'Student' : 'Donor' }}
                        <span class="urdu" dir="rtl">/ {{ $receipt->student ? 'طالب علم' : 'عطیہ دہندہ' }}</span>
                    </td>
                    <td class="px-6 py-3 text-right text-gray-900 dark:text-white">{{ $payer }}</td>
                </tr>
                @if($receipt->department)
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-6 py-3 text-gray-500">Department <span class="urdu" dir="rtl">/ محکمہ</span></td>
                    <td class="px-6 py-3 text-right text-gray-900 dark:text-white">{{ $receipt->department->name }}</td>
                </tr>
                @endif
                @if($receipt->mad)
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-6 py-3 text-gray-500">Head <span class="urdu" dir="rtl">/ مد</span></td>
                    <td class="px-6 py-3 text-right text-gray-900 dark:text-white">{{ $receipt->mad }}</td>
                </tr>
                @endif
                @if($receipt->given_to)
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-6 py-3 text-gray-500">Given To <span class="urdu" dir="rtl">/ کسے دی گئی</span></td>
                    <td class="px-6 py-3 text-right text-gray-900 dark:text-white">{{ $receipt->given_to }}</td>
                </tr>
                @endif
                <tr class="bg-gray-50 dark:bg-gray-700/40">
                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Amount <span class="urdu" dir="rtl">/ رقم</span></td>
                    <td class="px-6 py-4 text-right text-lg font-bold text-gray-900 dark:text-white">PKR {{ number_format((float) $receipt->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($receipt->description)
            <div class="px-6 py-3 text-xs text-gray-500 border-t border-gray-100 dark:border-gray-700">{{ $receipt->description }}</div>
        @endif

        {{-- Footer signatures --}}
        <div class="px-6 py-6 flex justify-between items-end text-sm">
            <div>
                <span class="text-gray-500">Received By / <span class="urdu" dir="rtl">وصول کنندہ</span>:</span>
                <span class="text-gray-900 dark:text-white">{{ $receipt->createdBy?->name ?? '—' }}</span>
            </div>
            <div class="text-right">
                <div class="border-t border-gray-400 dark:border-gray-500 w-40 pt-1 mt-6"></div>
                <span class="text-gray-500">Signature / <span class="urdu" dir="rtl">دستخط</span></span>
            </div>
        </div>
    </div>

    @if($autoPrint)
    @push('scripts')
    <script>
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 300); });
    </script>
    @endpush
    @endif

</x-layouts.backend-layout>
