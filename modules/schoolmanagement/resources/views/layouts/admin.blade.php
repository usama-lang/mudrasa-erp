@extends('schoolmanagement::layouts.master')

@section('schoolmanagement-admin-content')
    <div class="space-y-6">
        <div>
            {{ $slot }}
        </div>
    </div>
@endsection

