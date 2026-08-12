@extends('backend.layouts.app')

@section('title')
    @yield('schoolmanagement-title', $breadcrumbs['title'] ?? __('SchoolManagement')) | {{ __('SchoolManagement') }} | {{ config('app.name') }}
@endsection

@push('styles')
    @vite(['modules/SchoolManagement/resources/assets/css/app.css'], 'build-schoolmanagement')
@endpush

@section('admin-content')
    <div class="schoolmanagement-module container px-6 py-8 mx-auto min-h-[80vh]">
        @yield('schoolmanagement-admin-content')
    </div>
@endsection

@push('scripts')
    @stack('schoolmanagement-scripts')
@endpush

