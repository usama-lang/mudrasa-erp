<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form
            action="{{ route('school.sections.update', $section->id) }}"
            method="POST"
            data-prevent-unsaved-changes
        >
            @csrf
            @method('PUT')
            @include('schoolmanagement::pages.sections.partials.form', ['section' => $section])
        </form>
    </x-card>
</x-layouts.backend-layout>

