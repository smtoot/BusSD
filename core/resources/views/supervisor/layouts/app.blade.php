@extends('supervisor.layouts.master')
@php
    $sidenav = file_get_contents(resource_path('views/supervisor/partials/sidenav.json'));
@endphp
@section('content')
    <div class="page-wrapper default-version">
        @include('supervisor.partials.sidenav')
        @include('supervisor.partials.topnav')

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">
                    @stack('topBar')
                    @include('supervisor.partials.breadcrumb')

                    @yield('panel')
                </div>
            </div>
        </div>
    </div>
@endsection
