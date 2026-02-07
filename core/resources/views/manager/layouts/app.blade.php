@extends('manager.layouts.master')
@php
    $sidenav = file_get_contents(resource_path('views/manager/partials/sidenav.json'));
@endphp
@section('content')
    <div class="page-wrapper default-version">
        @include('manager.partials.sidenav')
        @include('manager.partials.topnav')

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">
                    @stack('topBar')
                    @include('manager.partials.breadcrumb')

                    @yield('panel')
                </div>
            </div>
        </div>
    </div>
@endsection
