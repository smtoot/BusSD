@extends('co_owner.layouts.master')
@php
    $sidenav = file_get_contents(resource_path('views/co_owner/partials/sidenav.json'));
@endphp
@section('content')
    <div class="page-wrapper default-version">
        @include('co_owner.partials.sidenav')
        @include('co_owner.partials.topnav')

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">
                    @stack('topBar')
                    @include('co_owner.partials.breadcrumb')

                    @yield('panel')
                </div>
            </div>
        </div>
    </div>
@endsection
