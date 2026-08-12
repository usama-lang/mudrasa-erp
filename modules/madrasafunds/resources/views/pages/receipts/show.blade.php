<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="flex flex-wrap justify-end gap-2 mb-4">
        @can('madrasa_funds.print_receipt')
            <a href="{{ route('admin.madrasafunds.receipts.print', $receipt) }}" class="btn btn-primary">
                <iconify-icon icon="lucide:printer"></iconify-icon> {{ mf_bi('Print Receipt') }}
            </a>
        @endcan
        @can('madrasa_funds.edit')
            @unless($receipt->is_cancelled)
                <a href="{{ route('admin.madrasafunds.receipts.edit', $receipt) }}" class="btn btn-default">
                    <iconify-icon icon="lucide:pencil"></iconify-icon> {{ mf_bi('Edit') }}
                </a>
            @endunless
        @endcan
        <a href="{{ route('admin.madrasafunds.receipts.index') }}" class="btn btn-default">{{ mf_bi('Back to List') }}</a>
    </div>

    @if($receipt->is_cancelled)
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm text-red-700 dark:text-red-300">
            <strong>{{ mf_bi('Cancelled') }}:</strong> {{ $receipt->cancelled_reason }}
        </div>
    @endif

    <x-card>
        <x-slot name="title">{{ mf_bi('Receipt Information') }}</x-slot>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">{{ mf_bi('Receipt No.') }}</dt><dd class="font-mono font-semibold text-gray-900 dark:text-white">{{ $receipt->receipt_number }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Receipt Date') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->receipt_date?->format('F d, Y') }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Fund') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->fund?->name }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Department') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->department?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Student') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->student?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Donor Name') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->donor_name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Amount') }}</dt><dd class="text-lg font-bold text-gray-900 dark:text-white">PKR {{ number_format((float) $receipt->amount, 2) }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Head (Mad)') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->mad ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Given To') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->given_to ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">{{ mf_bi('Created By') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->createdBy?->name ?? '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">{{ mf_bi('Description') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->description ?? '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">{{ mf_bi('Notes') }}</dt><dd class="text-gray-900 dark:text-white">{{ $receipt->notes ?? '—' }}</dd></div>
        </dl>

        @can('madrasa_funds.edit')
            @unless($receipt->is_cancelled)
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4" x-data="{ open: false }">
                    <button type="button" class="btn btn-danger btn-sm" @click="open = !open" :aria-expanded="open" aria-label="{{ mf_bi('Cancel Receipt') }}">
                        <iconify-icon icon="lucide:ban"></iconify-icon> {{ mf_bi('Cancel Receipt') }}
                    </button>
                    <form x-show="open" x-cloak action="{{ route('admin.madrasafunds.receipts.cancel', $receipt) }}" method="POST" class="mt-3 flex flex-col sm:flex-row gap-2"
                        onsubmit="return confirm('{{ mf_bi('Cancel this receipt?') }}')">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="cancelled_reason" class="form-control flex-1" placeholder="{{ mf_bi('Cancellation Reason') }}" required>
                        <button type="submit" class="btn btn-danger">{{ mf_bi('Cancel Receipt') }}</button>
                    </form>
                </div>
            @endunless
        @endcan
    </x-card>

</x-layouts.backend-layout>
