<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.sections.store') }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @include('schoolmanagement::pages.sections.partials.form', ['section' => null])
        </form>
    </x-card>
</x-layouts.backend-layout>

