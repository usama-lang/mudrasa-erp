@extends('backend.layouts.app')

@section('title')
    @yield('madrasafunds-title', $breadcrumbs['title'] ?? __('MadrasaFunds')) | {{ __('MadrasaFunds') }} | {{ config('app.name') }}
@endsection

@push('styles')
    @vite(['modules/MadrasaFunds/resources/assets/css/app.css'], 'build-madrasafunds')
@endpush

@section('admin-content')
    <div class="madrasafunds-module container px-6 py-8 mx-auto min-h-[80vh]">
        @yield('madrasafunds-admin-content')
    </div>
@endsection

@push('scripts')
    @stack('madrasafunds-scripts')
@endpush
