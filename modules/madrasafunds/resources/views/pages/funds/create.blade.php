<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.funds.store') }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @include('madrasafunds::pages.funds.partials.form', ['fund' => $fund])
        </form>
    </x-card>
</x-layouts.backend-layout>
