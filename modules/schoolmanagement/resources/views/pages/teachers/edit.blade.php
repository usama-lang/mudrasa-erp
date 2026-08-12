<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.teachers.update', $teacher->id) }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')
            @include('schoolmanagement::pages.teachers.partials.form', ['teacher' => $teacher])
        </form>
    </x-card>
</x-layouts.backend-layout>

