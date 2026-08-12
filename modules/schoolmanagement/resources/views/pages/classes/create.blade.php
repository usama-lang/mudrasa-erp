<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.classes.store') }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @include('schoolmanagement::pages.classes.partials.form', ['schoolClass' => null])
        </form>
    </x-card>
</x-layouts.backend-layout>

