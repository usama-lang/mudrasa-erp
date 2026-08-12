<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.funds.update', $fund) }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @method('PUT')
            @include('madrasafunds::pages.funds.partials.form', ['fund' => $fund])
        </form>
    </x-card>
</x-layouts.backend-layout>
