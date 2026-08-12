<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.departments.update', $department) }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @method('PUT')
            @include('madrasafunds::pages.departments.partials.form', ['department' => $department])
        </form>
    </x-card>
</x-layouts.backend-layout>
