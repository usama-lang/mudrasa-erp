<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.campuses.update', $campus->id) }}"
            method="POST"
            enctype="multipart/form-data"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')
            @include('schoolmanagement::pages.campuses.partials.form', ['campus' => $campus])
        </form>
    </x-card>
</x-layouts.backend-layout>

