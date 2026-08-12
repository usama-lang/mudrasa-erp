<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.students.store') }}"
            method="POST"
            enctype="multipart/form-data"
            data-prevent-unsaved-changes
        >
            @csrf
            @include('schoolmanagement::pages.students.partials.form', ['student' => null])
        </form>
    </x-card>
</x-layouts.backend-layout>

