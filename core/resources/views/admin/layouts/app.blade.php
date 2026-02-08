@extends('admin.layouts.master')
@section('content')
@php
    $sidenav = file_get_contents(resource_path('views/admin/partials/sidenav.json'));
@endphp
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">
        @include('admin.partials.sidenav')
        @include('admin.partials.topnav')

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">

                    @stack('topBar')
                    @include('admin.partials.breadcrumb')

                    @yield('panel')

                </div><!-- bodywrapper__inner end -->
            </div><!-- body-wrapper end -->
        </div>
    </div>

    <x-config-process />
@endsection

@push('style')
<style>
    /* ==========================================================
       REKAZ.IO THEME — Complete Admin UI Harmonization
       White sidebar, light topnav, red accent (#ef5050), modern
       ========================================================== */

    /* --- Global Font --- */
    .page-wrapper {
        font-family: 'IBM Plex Sans Arabic', 'Poppins', sans-serif;
    }

    /* --- Page Background --- */
    .body-wrapper,
    .bodywrapper__inner {
        background: #f3f4f6 !important;
    }

    /* --- Page Title --- */
    .page-title {
        font-weight: 700;
        color: #111827;
        font-size: 1.125rem;
    }

    /* ===================== SIDEBAR ===================== */
    .rk-sidebar {
        background: #ffffff !important;
        border-right: 1px solid #e5e7eb !important;
        box-shadow: 1px 0 8px rgba(0,0,0,0.04);
    }
    .rk-sidebar .sidebar__logo {
        padding: 20px 16px;
        border-bottom: 1px solid #f3f4f6;
    }
    .rk-sidebar .sidebar__logo .sidebar__main-logo img { max-height: 60px; }
    .rk-sidebar .res-sidebar-close-btn { background-color: #ef5050; color: #fff; }
    .rk-sidebar .sidebar__menu .sidebar__menu-header {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.08em; color: #9ca3af; margin: 24px 0 8px 20px;
    }
    .rk-sidebar .sidebar__menu .sidebar-menu-item > a {
        padding: 10px 20px; border-left: 3px solid transparent;
        border-radius: 0 8px 8px 0; margin-right: 12px; transition: all 150ms ease;
    }
    .rk-sidebar .sidebar__menu .sidebar-menu-item > a:hover { background-color: rgba(239,80,80,0.06); padding-left: 20px; }
    .rk-sidebar .sidebar__menu .sidebar-menu-item > a:hover .menu-icon { color: #ef5050; text-shadow: none; }
    .rk-sidebar .sidebar__menu .sidebar-menu-item > a:hover .menu-title { color: #ef5050; }
    .rk-sidebar .sidebar__menu .sidebar-menu-item .menu-icon { color: #6b7280; text-shadow: none; font-size: 1.125rem; transition: color 150ms ease; }
    .rk-sidebar .sidebar__menu .sidebar-menu-item .menu-title { color: #374151; font-size: 13px; font-weight: 500; letter-spacing: 0; }
    .rk-sidebar .sidebar__menu .sidebar-menu-item.active > a,
    .rk-sidebar .sidebar__menu .sidebar-menu-item .side-menu--open {
        background-color: rgba(239,80,80,0.08) !important; background-image: none !important; border-left-color: #ef5050;
    }
    .rk-sidebar .sidebar__menu .sidebar-menu-item.active > a .menu-icon,
    .rk-sidebar .sidebar__menu .sidebar-menu-item.active > a .menu-title,
    .rk-sidebar .sidebar__menu .sidebar-menu-item .side-menu--open .menu-icon,
    .rk-sidebar .sidebar__menu .sidebar-menu-item .side-menu--open .menu-title { color: #ef5050 !important; }
    .rk-sidebar .sidebar__menu .sidebar-dropdown > a::before { color: #9ca3af; }
    .rk-sidebar .sidebar__menu .sidebar-dropdown > a:hover::before,
    .rk-sidebar .sidebar__menu .sidebar-dropdown > a.side-menu--open::before { color: #ef5050; }
    .rk-sidebar .sidebar__menu .sidebar-submenu { background-color: #fafafa; }
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item a { padding: 8px 20px 8px 35px; }
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item a .menu-icon { color: #9ca3af; font-size: 0.625rem; }
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item a .menu-title { color: #6b7280; font-size: 12.5px; }
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item.active > a { background-color: rgba(239,80,80,0.06) !important; }
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item.active a .menu-icon,
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item.active a .menu-title { color: #ef5050 !important; }
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item a:hover .menu-icon,
    .rk-sidebar .sidebar__menu .sidebar-submenu .sidebar-menu-item a:hover .menu-title { color: #ef5050; }
    .rk-sidebar .sidebar__menu .menu-badge { box-shadow: none; border-radius: 100px; font-size: 10px; font-weight: 700; padding: 2px 8px; }
    .rk-sidebar .menu-badge.bg--warning { background: rgba(249,115,22,0.12) !important; color: #f97316 !important; }
    .rk-sidebar .menu-badge.bg--info { background: rgba(239,80,80,0.1) !important; color: #ef5050 !important; }
    .rk-sidebar .sidebar__menu-wrapper::-webkit-scrollbar { width: 4px; }
    .rk-sidebar .sidebar__menu-wrapper::-webkit-scrollbar-track { background: transparent; }
    .rk-sidebar .sidebar__menu-wrapper::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 100px; }
    .rk-sidebar .sidebar__menu-wrapper::-webkit-scrollbar-thumb:hover { background: #d1d5db !important; }
    .rk-version { background-color: #f9fafb !important; border-top: 1px solid #f3f4f6; padding-block: 12px; width: 100%; }
    .rk-version span { color: #9ca3af !important; font-size: 10px; font-weight: 500; letter-spacing: 0.05em; }

    /* ===================== TOP NAVBAR ===================== */
    .rk-topnav {
        background: #ffffff !important; border-bottom: 1px solid #e5e7eb !important;
        padding: 12px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    @media (max-width: 575px) { .rk-topnav { padding: 10px 12px; } }
    .rk-topnav .navbar-search .navbar-search-field {
        background-color: #f9fafb; color: #111827; border: 1px solid #e5e7eb;
        border-radius: 10px; font-size: 13px; padding: 8px 12px 8px 40px; transition: all 150ms ease;
    }
    .rk-topnav .navbar-search .navbar-search-field::placeholder { color: #9ca3af; }
    .rk-topnav .navbar-search .navbar-search-field:focus {
        border-color: #ef5050; box-shadow: 0 0 0 3px rgba(239,80,80,0.1); background: #fff;
    }
    .rk-topnav .navbar-search i { color: #9ca3af; }
    .rk-topnav .search-list { border-radius: 10px !important; border: 1px solid #e5e7eb !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; overflow: hidden; }
    .rk-topnav .search-list li .search-list-link:hover,
    .rk-topnav .search-list li.active .search-list-link { background: rgba(239,80,80,0.06); color: #ef5050; }
    .rk-topnav .res-sidebar-open-btn { color: #374151; }
    .rk-topnav .navbar__right button i { color: #6b7280 !important; text-shadow: none; font-size: 1.35rem; transition: color 150ms ease; }
    .rk-topnav .navbar__right button:hover i,
    .rk-topnav .navbar__right a:hover i { color: #ef5050 !important; }
    .rk-topnav .primary--layer a i { color: #6b7280; transition: color 150ms ease; }
    .rk-topnav .primary--layer a:hover i { color: #ef5050 !important; }
    .rk-topnav .navbar-user__name { color: #111827 !important; font-weight: 600; font-size: 13px; }
    .rk-topnav .navbar-user__thumb img { border: 2px solid #e5e7eb; }
    .rk-topnav .navbar-user .icon i { color: #9ca3af; }
    .rk-topnav .dropdown-menu { border: 1px solid #e5e7eb !important; border-radius: 12px !important; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; overflow: hidden; padding: 0 !important; }
    .rk-topnav .dropdown-menu__item { border-bottom: 1px solid #f3f4f6; transition: all 150ms ease; }
    .rk-topnav .dropdown-menu__item:hover { background-color: #f9fafb; }
    .rk-topnav .dropdown-menu__item .dropdown-menu__icon { color: #6b7280; text-shadow: none; }
    .rk-topnav .dropdown-menu__item:hover .dropdown-menu__icon { color: #ef5050; }
    .rk-topnav .dropdown-menu__item .dropdown-menu__caption { color: #374151; font-size: 13px; font-weight: 500; }

    /* ===================== BREADCRUMB ===================== */
    .page-breadcrumb li a { color: #ef5050; }

    /* ===================== CARDS ===================== */
    .card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .card-header {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 12px 12px 0 0 !important;
    }
    .card-body { padding: 20px; }

    /* ===================== TABLES — COMPLETE OVERRIDE ===================== */
    table.table--light thead th {
        background-color: #1f2937 !important;
        color: #ffffff !important;
        border: none !important;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    table.table--light.style--two thead th {
        background-color: #1f2937 !important;
    }
    .table th {
        font-size: 12px; font-weight: 600; color: #6b7280; border-color: #e5e7eb;
    }
    .table td {
        color: #374151; font-size: 13px; border-color: #f3f4f6;
    }
    .table tbody tr:hover td {
        background-color: #f9fafb;
    }

    /* ===================== BUTTONS — PRIMARY ===================== */
    .btn--primary {
        --color: #ef5050 !important;
        background-color: #ef5050 !important;
        border: 1px solid #ef5050 !important;
    }
    .btn--primary:hover,
    .btn--primary:active,
    .btn--primary:focus {
        background-color: #dc4545 !important;
        border-color: #dc4545 !important;
        box-shadow: 0 4px 12px rgba(239,80,80,0.3) !important;
    }
    .btn-outline--primary {
        --color: #ef5050 !important;
        color: #ef5050 !important;
        border-color: #ef5050 !important;
    }
    .btn-outline--primary:hover {
        background-color: #ef5050 !important;
        color: #fff !important;
    }

    /* ===================== BADGES ===================== */
    .badge--primary { background-color: rgba(239,80,80,0.1) !important; border: 1px solid #ef5050 !important; color: #ef5050 !important; }
    .badge--success { background-color: rgba(5,150,105,0.1) !important; border: 1px solid #059669 !important; color: #059669 !important; }
    .badge--danger { background-color: rgba(239,68,68,0.1) !important; border: 1px solid #ef4444 !important; color: #ef4444 !important; }
    .badge--warning { background-color: rgba(249,115,22,0.1) !important; border: 1px solid #f97316 !important; color: #f97316 !important; }

    /* ===================== FORM INPUTS ===================== */
    .form-control:focus {
        border-color: #ef5050 !important;
        box-shadow: 0 0 0 3px rgba(239,80,80,0.08) !important;
    }

    /* ===================== PAGINATION ===================== */
    .page-item.active .page-link { background-color: #ef5050 !important; border-color: #ef5050 !important; }
    .page-link { color: #ef5050; }

    /* ===================== MISC ===================== */
    .bg--primary { background-color: #ef5050 !important; }
    .text--primary { color: #ef5050 !important; }
</style>
@endpush
