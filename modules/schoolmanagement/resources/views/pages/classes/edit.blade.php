<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.classes.update', $schoolClass->id) }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')
            @include('schoolmanagement::pages.classes.partials.form', ['schoolClass' => $schoolClass])
        </form>
    </x-card>
</x-layouts.backend-layout>

