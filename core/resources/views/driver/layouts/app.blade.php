@extends('driver.layouts.master')
@php
    $sidenav = file_get_contents(resource_path('views/driver/partials/sidenav.json'));
@endphp
@section('content')
    <div class="page-wrapper default-version">
        @include('driver.partials.sidenav')
        @include('driver.partials.topnav')

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">
                    @stack('topBar')
                    @include('driver.partials.breadcrumb')

                    @yield('panel')
                </div>
            </div>
        </div>
    </div>
@endsection
