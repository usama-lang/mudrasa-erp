<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <form action="{{ route('admin.madrasafunds.students.update', $student) }}" method="POST" data-prevent-unsaved-changes>
            @csrf
            @method('PUT')
            @include('madrasafunds::pages.students.partials.form', ['student' => $student])
        </form>
    </x-card>
</x-layouts.backend-layout>
