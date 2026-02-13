@php
    $sidenav = json_decode($sidenav);
    $settings = file_get_contents(resource_path('views/owner/setting/settings.json'));
    $settings = json_decode($settings);
    $routesData = [];
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $name = $route->getName();
        if (strpos($name, 'owner') !== false) {
            $routeData = [
                $name => url($route->uri()),
            ];
            $routesData[] = $routeData;
        }
    }

    $languages = \App\Models\Language::all();
@endphp

<!-- navbar-wrapper start -->
<nav class="navbar-wrapper rk-topnav d-flex flex-wrap">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                placeholder="@lang('Search here...')">
            <i class="las la-search"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            {{-- Balance Display --}}
            <li class="d-none d-sm-inline-block">
                <a href="{{ route('owner.withdraw.methods') }}" class="rk-balance-btn">
                    <i class="las la-wallet"></i>
                    <span>@lang('Balance'): <strong>{{ gs('cur_sym') }}{{ showAmount(authUser()->balance) }}</strong></span>
                </a>
            </li>
            <li>
                <button type="button" class="primary--layer" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    title="@lang('Visit Website')">
                    <a href="{{ route('home') }}" target="_blank"><i class="las la-globe"></i></a>
                </button>
            </li>
            <li class="dropdown">
                <button type="button" class="primary--layer" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false" title="@lang('Change Language')">
                    <i class="las la-language"></i>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    @foreach($languages as $lang)
                        <a href="{{ route('lang', $lang->code) }}"
                            class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <span class="dropdown-menu__caption">{{ __($lang->name) }}</span>
                        </a>
                    @endforeach
                </div>
            </li>
            <li>
                <button type="button" class="primary--layer" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    title="@lang('Manage Transport')">
                    <a href="{{ route('owner.manage.transport') }}"><i class="las la-wrench"></i></a>
                </button>
            </li>
            {{-- B2C Notifications --}}
            <li class="dropdown">
                <button type="button" class="primary--layer" data-bs-toggle="dropdown" data-bs-placement="bottom"
                    title="@lang('App Bookings')" id="notificationButton">
                    <i class="las la-bell"></i>
                    <span class="badge badge--danger notification-badge" id="notificationCount" style="display: none;">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right p-0 border-0 box--shadow1" style="min-width: 350px; max-height: 450px; overflow-y: auto;">
                    <div class="dropdown-header rk-notif-header d-flex justify-content-between align-items-center px-3 py-2">
                        <span><i class="las la-mobile"></i> @lang('Recent App Bookings')</span>
                        <small id="notificationTime">@lang('Last 24 hours')</small>
                    </div>
                    <div id="notificationList" class="notification-list">
                        <div class="text-center py-4">
                            <i class="las la-spinner la-spin" style="font-size: 24px;"></i>
                            <p class="text-muted mt-2">@lang('Loading...')</p>
                        </div>
                    </div>
                    <div class="dropdown-footer text-center border-top">
                        <a href="{{ route('owner.report.sale.b2c') }}" class="dropdown-menu__item d-block px-3 py-2">
                            @lang('View All App Sales') <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </li>
            <li class="dropdown d-flex profile-dropdown">
                <button type="button" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true"
                    aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ getImage(getFilePath('ownerProfile') . '/' . authUser()->image, getFileSize('ownerProfile')) }}"
                                alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ authUser()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('owner.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a href="{{ route('owner.password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a href="{{ route('owner.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
                <button type="button" class="breadcrumb-nav-open ms-2 d-none">
                    <i class="las la-sliders-h"></i>
                </button>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->

@push('style')
<style>
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 18px;
        height: 18px;
        padding: 2px 5px;
        font-size: 10px;
        line-height: 14px;
        border-radius: 9px;
        background: #ef5050 !important;
        color: #fff;
    }

    .notification-item {
        transition: background-color 0.15s;
    }

    .notification-item:hover {
        background-color: #f9fafb;
    }

    .notification-list {
        max-height: 350px;
        overflow-y: auto;
    }

    .rk-notif-header {
        background: #ef5050 !important;
        color: #fff !important;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
    }
    .rk-notif-header small {
        color: rgba(255,255,255,0.8);
    }

    .dropdown-footer a {
        color: #ef5050;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
    }

    .dropdown-footer a:hover {
        color: #dc4545;
    }

    #notificationButton {
        position: relative;
    }
</style>
@endpush

@push('script')
    <script>
        "use strict";
        var routes = @json($routesData);
        var settingsData = Object.assign({}, @json($settings), @json($sidenav));

        $('.navbar__action-list .dropdown-menu').on('click', function(event) {
            event.stopPropagation();
        });
    </script>
    <script src="{{ asset('assets/admin/js/search.js') }}"></script>
    <script>
        "use strict";

        function getEmptyMessage() {
            return `<li class="text-muted">
                <div class="empty-search text-center">
                    <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                    <p class="text-muted">No search result found</p>
                </div>
            </li>`
        }

        // B2C Notifications
        function loadNotifications() {
            $.ajax({
                url: '{{ route("owner.notifications.b2c") }}',
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        updateNotificationUI(response.data, response.count);
                    }
                },
                error: function() {
                    $('#notificationList').html(`
                        <div class="text-center py-4">
                            <i class="las la-exclamation-circle text-danger" style="font-size: 24px;"></i>
                            <p class="text-muted mt-2">Failed to load notifications</p>
                        </div>
                    `);
                }
            });
        }

        function updateNotificationUI(notifications, count) {
            const badge = $('#notificationCount');
            const list = $('#notificationList');

            // Update badge
            if (count > 0) {
                badge.text(count).show();
            } else {
                badge.hide();
            }

            // Update list
            if (notifications.length === 0) {
                list.html(`
                    <div class="text-center py-4">
                        <i class="las la-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-2">No new app bookings</p>
                        <small class="text-muted">Check back later</small>
                    </div>
                `);
            } else {
                notifications.forEach(notification => {
                    html += `
                        <div class="notification-item border-bottom px-3 py-3" style="cursor: pointer;" onclick="window.location.href='{{ route("owner.report.sale.b2c") }}'">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon me-3">
                                    <i class="las la-ticket-alt text--success" style="font-size: 24px;"></i>
                                </div>
                                <div class="notification-content flex-grow-1">
                                    <p class="mb-1"><strong>${notification.passenger_name}</strong> {{ __('booked') }} <strong>${notification.seats} {{ __('seat(s)') }}</strong></p>
                                    <p class="mb-1 text-muted" style="font-size: 13px;">${notification.trip_title}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="badge badge--success">{{ gs('cur_sym') }}${notification.amount}</span>
                                        <small class="text-muted">${notification.time}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                list.html(html);
            }
        }

        // Load notifications on page load
        $(document).ready(function() {
            loadNotifications();

            // Refresh notifications every 60 seconds
            setInterval(loadNotifications, 60000);

            // Reload when dropdown is opened
            $('#notificationButton').on('click', function() {
                loadNotifications();
            });
        });
    </script>
@endpush
