<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.students.update', $student->id) }}"
            method="POST"
            enctype="multipart/form-data"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')
            @include('schoolmanagement::pages.students.partials.form', ['student' => $student])
        </form>
    </x-card>
</x-layouts.backend-layout>

