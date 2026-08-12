<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.receipts.update', $receipt) }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @method('PUT')
            @include('madrasafunds::pages.receipts.partials.form', ['receipt' => $receipt])
        </form>
    </x-card>
</x-layouts.backend-layout>
