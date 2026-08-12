<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.departments.update', $department->id) }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')
            @include('schoolmanagement::pages.departments.partials.form', ['department' => $department])
        </form>
    </x-card>
</x-layouts.backend-layout>

