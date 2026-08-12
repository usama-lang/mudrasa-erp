<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.departments.store') }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @include('schoolmanagement::pages.departments.partials.form', ['department' => null])
        </form>
    </x-card>
</x-layouts.backend-layout>

