<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.campuses.store') }}"
            method="POST"
            enctype="multipart/form-data"
            data-prevent-unsaved-changes
        >
            @csrf
            @include('schoolmanagement::pages.campuses.partials.form', ['campus' => null])
        </form>
    </x-card>
</x-layouts.backend-layout>

