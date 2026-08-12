<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.teachers.store') }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @include('schoolmanagement::pages.teachers.partials.form', ['teacher' => null])
        </form>
    </x-card>
</x-layouts.backend-layout>

