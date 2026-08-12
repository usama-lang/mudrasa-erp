<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.students.store') }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @include('madrasafunds::pages.students.partials.form', ['student' => $student])
        </form>
    </x-card>
</x-layouts.backend-layout>
