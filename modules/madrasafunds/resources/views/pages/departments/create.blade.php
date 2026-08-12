<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.departments.store') }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @include('madrasafunds::pages.departments.partials.form', ['department' => $department])
        </form>
    </x-card>
</x-layouts.backend-layout>
