<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.receipts.store') }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @include('madrasafunds::pages.receipts.partials.form', ['receipt' => $receipt])
        </form>
    </x-card>
</x-layouts.backend-layout>
