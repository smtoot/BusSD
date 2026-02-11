@extends('owner.layouts.app')
@section('panel')
    <div class="rk-dashboard">
        <div class="notice"></div>
        @if (count($widget['active_packages']) == 0)
            <div class="rk-alert">
                <div class="rk-alert__icon"><i class="far fa-bell"></i></div>
                <div class="rk-alert__content">
                    <p class="rk-alert__title">@lang('No Active Package')</p>
                    <p class="rk-alert__text">@lang('You\'ve no active package. Please buy a package from')
                        <a href="{{ route('owner.package.index') }}">@lang('here')</a>
                    </p>
                </div>
            </div>
        @endif

        {{-- Row 1: Today's Snapshot — single white card with 4 inline stats --}}
        <div class="rk-section">
            <div class="rk-card rk-today-strip">
                <div class="rk-today-strip__header">
                    <h6 class="rk-section__title rk-section__title--inline">@lang("Today's Snapshot")</h6>
                    <div class="rk-quick-links">
                        <a href="{{ route('owner.trip.form') }}" class="rk-quick-link"><i class="las la-plus-circle"></i> @lang('New Trip')</a>
                        <a href="{{ route('owner.report.sale.index') }}" class="rk-quick-link"><i class="las la-receipt"></i> @lang('Bookings')</a>
                        <a href="{{ route('owner.report.sale.b2c') }}" class="rk-quick-link"><i class="las la-mobile"></i> @lang('B2C Sales')</a>
                    </div>
                </div>
                <div class="rk-today-strip__grid">
                    <div class="rk-mini-stat">
                        <div class="rk-mini-stat__icon"><i class="las la-dollar-sign"></i></div>
                        <div>
                            <span class="rk-mini-stat__label">@lang('Revenue')</span>
                            <strong class="rk-mini-stat__value">{{ gs('cur_sym') }}{{ getAmount($widget['today_revenue']) }}</strong>
                        </div>
                    </div>
                    <div class="rk-mini-stat">
                        <div class="rk-mini-stat__icon"><i class="las la-ticket-alt"></i></div>
                        <div>
                            <span class="rk-mini-stat__label">@lang('Bookings')</span>
                            <strong class="rk-mini-stat__value">{{ $widget['today_bookings'] }}</strong>
                        </div>
                    </div>
                    <div class="rk-mini-stat">
                        <div class="rk-mini-stat__icon"><i class="las la-bus"></i></div>
                        <div>
                            <span class="rk-mini-stat__label">@lang('Trips Running')</span>
                            <strong class="rk-mini-stat__value">{{ $widget['today_trips'] }}</strong>
                        </div>
                    </div>
                    <div class="rk-mini-stat">
                        <div class="rk-mini-stat__icon"><i class="las la-users"></i></div>
                        <div>
                            <span class="rk-mini-stat__label">@lang('Passengers')</span>
                            <strong class="rk-mini-stat__value">{{ $widget['today_passengers'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Monthly Revenue — 4 KPI cards in a true 4-col grid --}}
        <div class="rk-section">
            <h6 class="rk-section__title">@lang('Monthly Revenue')</h6>
            <div class="rk-kpi-row">
                <div class="rk-kpi-card">
                    <div class="rk-kpi-card__top">
                        <div class="rk-kpi-card__icon rk-kpi-card__icon--red"><i class="las la-cash-register"></i></div>
                        <span class="rk-kpi-card__label">@lang('Counter Sales')</span>
                    </div>
                    <h4 class="rk-kpi-card__amount">{{ gs('cur_sym') }}{{ getAmount($widget['counter_sales']) }}</h4>
                    <div class="rk-kpi-card__footer">
                        @if($widget['counter_percent_change'] > 0)
                            <span class="rk-pill rk-pill--success"><i class="las la-arrow-up"></i> {{ number_format($widget['counter_percent_change'], 1) }}%</span>
                        @elseif($widget['counter_percent_change'] < 0)
                            <span class="rk-pill rk-pill--danger"><i class="las la-arrow-down"></i> {{ number_format(abs($widget['counter_percent_change']), 1) }}%</span>
                        @else
                            <span class="rk-pill rk-pill--muted"><i class="las la-minus"></i> @lang('Flat')</span>
                        @endif
                        <span class="rk-kpi-card__vs">@lang('vs last month')</span>
                    </div>
                </div>
                <div class="rk-kpi-card">
                    <div class="rk-kpi-card__top">
                        <div class="rk-kpi-card__icon rk-kpi-card__icon--emerald"><i class="las la-mobile"></i></div>
                        <span class="rk-kpi-card__label">@lang('B2C (App) Sales')</span>
                    </div>
                    <h4 class="rk-kpi-card__amount">{{ gs('cur_sym') }}{{ getAmount($widget['b2c_sales']) }}</h4>
                    <div class="rk-kpi-card__footer">
                        @if($widget['b2c_percent_change'] > 0)
                            <span class="rk-pill rk-pill--success"><i class="las la-arrow-up"></i> {{ number_format($widget['b2c_percent_change'], 1) }}%</span>
                        @elseif($widget['b2c_percent_change'] < 0)
                            <span class="rk-pill rk-pill--danger"><i class="las la-arrow-down"></i> {{ number_format(abs($widget['b2c_percent_change']), 1) }}%</span>
                        @else
                            <span class="rk-pill rk-pill--muted"><i class="las la-minus"></i> @lang('Flat')</span>
                        @endif
                        <span class="rk-kpi-card__vs">@lang('vs last month')</span>
                    </div>
                </div>
                <div class="rk-kpi-card">
                    <div class="rk-kpi-card__top">
                        <div class="rk-kpi-card__icon rk-kpi-card__icon--violet"><i class="las la-users"></i></div>
                        <span class="rk-kpi-card__label">@lang('App Passengers')</span>
                    </div>
                    <h4 class="rk-kpi-card__amount">{{ $widget['app_passengers'] }}</h4>
                    <div class="rk-kpi-card__footer">
                        <span class="rk-kpi-card__vs">@lang('Bookings this month')</span>
                    </div>
                </div>
                <div class="rk-kpi-card">
                    <div class="rk-kpi-card__top">
                        <div class="rk-kpi-card__icon rk-kpi-card__icon--amber"><i class="las la-coins"></i></div>
                        <span class="rk-kpi-card__label">@lang('B2C Revenue')</span>
                    </div>
                    <h4 class="rk-kpi-card__amount">{{ gs('cur_sym') }}{{ getAmount($widget['b2c_revenue']) }}</h4>
                    <div class="rk-kpi-card__footer">
                        @if($widget['b2c_revenue_percent_change'] > 0)
                            <span class="rk-pill rk-pill--success"><i class="las la-arrow-up"></i> {{ number_format($widget['b2c_revenue_percent_change'], 1) }}%</span>
                        @elseif($widget['b2c_revenue_percent_change'] < 0)
                            <span class="rk-pill rk-pill--danger"><i class="las la-arrow-down"></i> {{ number_format(abs($widget['b2c_revenue_percent_change']), 1) }}%</span>
                        @else
                            <span class="rk-pill rk-pill--muted"><i class="las la-minus"></i> @lang('Flat')</span>
                        @endif
                        <span class="rk-kpi-card__vs">@lang('vs last month')</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Chart (60%) + Top Routes (40%) side by side --}}
        <div class="rk-section">
            <div class="rk-split">
                <div class="rk-split__main">
                    <div class="rk-card rk-card--full">
                        <div class="rk-card__header">
                            <h6 class="rk-section__title rk-section__title--inline">@lang('Sales Comparison')</h6>
                            <div id="dwDatePicker" class="rk-date-btn">
                                <i class="la la-calendar"></i>&nbsp;
                                <span></span> <i class="la la-caret-down"></i>
                            </div>
                        </div>
                        <div id="dwChartArea"></div>
                    </div>
                </div>
                <div class="rk-split__side">
                    <div class="rk-card rk-card--full">
                        <h6 class="rk-section__title rk-section__title--inline mb-3">@lang('Top Routes')</h6>
                        @if($topRoutes->count() > 0)
                            <div class="rk-route-list">
                                @foreach($topRoutes as $route)
                                    <div class="rk-route-item">
                                        <div class="rk-route-item__info">
                                            <span class="rk-route-item__name">{{ $route['name'] }}</span>
                                            <span class="rk-route-item__meta">{{ $route['booking_count'] }} @lang('bookings') &middot; {{ gs('cur_sym') }}{{ getAmount($route['revenue']) }}</span>
                                        </div>
                                        <div class="rk-route-item__bar-wrap">
                                            <div class="rk-route-item__bar">
                                                <div class="rk-route-item__bar-fill" style="width: {{ $route['percentage'] }}%"></div>
                                            </div>
                                            <span class="rk-route-item__pct">{{ $route['percentage'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="rk-empty">@lang('No route data yet.')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Operations — compact horizontal strip --}}
        <div class="rk-section">
            <div class="rk-card">
                <h6 class="rk-section__title rk-section__title--inline mb-3">@lang('Operations')</h6>
                <div class="rk-ops-row">
                    <a href="{{ route('owner.vehicle.index') }}" class="rk-ops-chip">
                        <i class="las la-bus"></i>
                        <strong>{{ $widget['total_bus'] }}</strong>
                        <span>@lang('Vehicles')</span>
                    </a>
                    <a href="{{ route('owner.driver.index') }}" class="rk-ops-chip">
                        <i class="las la-user-astronaut"></i>
                        <strong>{{ $widget['total_driver'] }}</strong>
                        <span>@lang('Drivers')</span>
                    </a>
                    <a href="{{ route('owner.supervisor.index') }}" class="rk-ops-chip">
                        <i class="las la-user-tie"></i>
                        <strong>{{ $widget['total_supervisor'] }}</strong>
                        <span>@lang('Supervisors')</span>
                    </a>
                    <a href="{{ route('owner.co-owner.index') }}" class="rk-ops-chip">
                        <i class="las la-users"></i>
                        <strong>{{ $widget['total_coAdmin'] }}</strong>
                        <span>@lang('Co-Admins')</span>
                    </a>
                    <a href="{{ route('owner.trip.route.index') }}" class="rk-ops-chip">
                        <i class="las la-route"></i>
                        <strong>{{ $widget['total_route'] }}</strong>
                        <span>@lang('Routes')</span>
                    </a>
                    <a href="{{ route('owner.trip.index') }}" class="rk-ops-chip">
                        <i class="las la-radiation-alt"></i>
                        <strong>{{ $widget['total_trip'] }}</strong>
                        <span>@lang('Trips')</span>
                    </a>
                    <a href="{{ route('owner.counter.index') }}" class="rk-ops-chip">
                        <i class="las la-landmark"></i>
                        <strong>{{ $widget['total_counter'] }}</strong>
                        <span>@lang('Counters')</span>
                    </a>
                    <a href="{{ route('owner.counter.manager.index') }}" class="rk-ops-chip">
                        <i class="las la-user-alt"></i>
                        <strong>{{ $widget['total_counter_manager'] }}</strong>
                        <span>@lang('Counter Mgrs')</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
            ranges: {
                '{{ __("Today") }}': [moment(), moment()],
                '{{ __("Yesterday") }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ __("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ __("Last 15 Days") }}': [moment().subtract(14, 'days'), moment()],
                '{{ __("Last 30 Days") }}': [moment().subtract(30, 'days'), moment()],
                '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                '{{ __("Last 6 Months") }}': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                '{{ __("This Year") }}': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMM D') + ' - ' + endDate.format('MMM D, YYYY'));
        }

        let dwChart = new ApexCharts(document.querySelector("#dwChartArea"), {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: true,
                    tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }
                },
                fontFamily: "'IBM Plex Sans Arabic', 'Poppins', sans-serif",
                foreColor: '#6b7280',
                background: 'transparent',
            },
            colors: ['#ef5050', '#1f2937'],
            series: [
                { name: '{{ __("B2C (App)") }}', data: [] },
                { name: '{{ __("Counter") }}', data: [] }
            ],
            xaxis: {
                categories: [],
                labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 500 } },
                axisBorder: { color: '#e5e7eb' },
                axisTicks: { color: '#e5e7eb' }
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 500 },
                    formatter: function(val) { return @json(gs('cur_sym')) + val; }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 0,
                xaxis: { lines: { show: false } }
            },
            plotOptions: {
                bar: { borderRadius: 5, columnWidth: '50%' }
            },
            dataLabels: { enabled: false },
            legend: {
                fontSize: '12px',
                fontWeight: 500,
                labels: { colors: '#6b7280' },
                markers: { radius: 3, width: 10, height: 10 },
                itemMargin: { horizontal: 12 }
            },
            tooltip: {
                theme: false,
                style: { fontSize: '13px' },
                y: { formatter: function(val) { return @json(gs('cur_sym')) + val; } }
            },
            states: {
                hover: { filter: { type: 'darken', value: 0.9 } }
            }
        });
        dwChart.render();

        const depositWithdrawChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }
            const url = @json(route('owner.chart.sales'));
            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: { categories: data.created_on }
                        });
                    }
                }
            );
        }

        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span', start, end));
        changeDatePickerText('#dwDatePicker span', start, end);
        depositWithdrawChart(start, end);
        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositWithdrawChart(picker.startDate, picker.endDate));
    </script>
@endpush

@push('style')
    <style>
        /* ============================================================
           REKAZ.IO DASHBOARD v2 — Compact, better-organized layout
           ============================================================ */

        .body-wrapper,
        .bodywrapper__inner {
            background: #f3f4f6 !important;
        }

        .rk-dashboard {
            font-family: 'IBM Plex Sans Arabic', 'Poppins', sans-serif;
        }

        /* --- Sections --- */
        .rk-section {
            margin-bottom: 20px;
        }
        .rk-section__title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 14px;
            letter-spacing: -0.01em;
        }
        .rk-section__title--inline {
            margin-bottom: 0;
        }

        /* --- Alert --- */
        .rk-alert {
            background: #fff;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .rk-alert__icon {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: #fef2f2; color: #ef4444; font-size: 18px; flex-shrink: 0;
        }
        .rk-alert__title { font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px; }
        .rk-alert__text { font-size: 12px; color: #6b7280; margin: 0; }
        .rk-alert__text a { color: #ef5050; font-weight: 600; text-decoration: none; }
        .rk-alert__text a:hover { text-decoration: underline; }

        /* --- Shared Card --- */
        .rk-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .rk-card--full { height: 100%; }
        .rk-card__header {
            display: flex; flex-wrap: wrap;
            justify-content: space-between;
            align-items: center; margin-bottom: 16px;
            gap: 10px;
        }

        /* ==================== ROW 1: TODAY STRIP ==================== */
        .rk-today-strip__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .rk-quick-links {
            display: flex; gap: 6px; flex-wrap: wrap;
        }
        .rk-quick-link {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
            color: #6b7280; text-decoration: none;
            background: #f9fafb; border: 1px solid #e5e7eb;
            transition: all 150ms ease;
        }
        .rk-quick-link:hover {
            background: #ef5050; color: #fff; border-color: #ef5050;
        }
        .rk-quick-link i { font-size: 15px; }

        .rk-today-strip__grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
        }
        .rk-mini-stat {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px;
            border-right: 1px solid #f3f4f6;
        }
        [dir="rtl"] .rk-mini-stat {
            border-right: none;
            border-left: 1px solid #f3f4f6;
        }
        .rk-mini-stat:last-child { border-right: none; border-left: none; }
        .rk-mini-stat__icon {
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: #f3f4f6; color: #6b7280; font-size: 20px; flex-shrink: 0;
            transition: all 150ms ease;
        }
        .rk-mini-stat:hover .rk-mini-stat__icon {
            background: rgba(239,80,80,0.08); color: #ef5050;
        }
        .rk-mini-stat__label {
            display: block; font-size: 11px; font-weight: 500;
            color: #9ca3af; margin-bottom: 2px;
        }
        .rk-mini-stat__value {
            font-size: 22px; font-weight: 800; color: #111827;
            line-height: 1.1; letter-spacing: -0.02em;
        }

        /* ==================== ROW 2: KPI CARDS ==================== */
        .rk-kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }
        .rk-kpi-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 150ms ease;
        }
        .rk-kpi-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.07);
            border-color: rgba(239,80,80,0.25);
            transform: translateY(-2px);
        }
        .rk-kpi-card__top {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 12px;
        }
        .rk-kpi-card__icon {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .rk-kpi-card__icon--red { background: rgba(239,80,80,0.1); color: #ef5050; }
        .rk-kpi-card__icon--emerald { background: rgba(5,150,105,0.1); color: #059669; }
        .rk-kpi-card__icon--violet { background: rgba(139,92,246,0.1); color: #8b5cf6; }
        .rk-kpi-card__icon--amber { background: rgba(249,115,22,0.1); color: #f97316; }
        .rk-kpi-card__label { font-size: 12px; font-weight: 500; color: #6b7280; }
        .rk-kpi-card__amount {
            font-size: 24px; font-weight: 800; color: #111827;
            margin: 0 0 8px; letter-spacing: -0.02em; line-height: 1.1;
        }
        .rk-kpi-card__footer {
            display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
        }
        .rk-kpi-card__vs { font-size: 11px; color: #9ca3af; }

        /* --- Pills --- */
        .rk-pill {
            display: inline-flex; align-items: center; gap: 2px;
            font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px;
        }
        .rk-pill--success { background: rgba(5,150,105,0.1); color: #059669; }
        .rk-pill--danger { background: rgba(239,68,68,0.1); color: #ef4444; }
        .rk-pill--muted { background: #f3f4f6; color: #9ca3af; }

        /* ==================== ROW 3: CHART + ROUTES ==================== */
        .rk-split {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 16px;
            align-items: stretch;
        }

        /* Date Picker */
        .rk-date-btn {
            padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 500;
            color: #6b7280; cursor: pointer; background: #f9fafb;
            border: 1px solid #e5e7eb; transition: all 150ms ease;
            white-space: nowrap;
        }
        .rk-date-btn:hover { border-color: #d1d5db; background: #fff; }

        /* Route List */
        .rk-route-list {
            display: flex; flex-direction: column; gap: 14px;
        }
        .rk-route-item__info {
            display: flex; flex-direction: column; margin-bottom: 6px;
        }
        .rk-route-item__name {
            font-size: 13px; font-weight: 600; color: #111827;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .rk-route-item__meta {
            font-size: 11px; color: #9ca3af; margin-top: 1px;
        }
        .rk-route-item__bar-wrap {
            display: flex; align-items: center; gap: 8px;
        }
        .rk-route-item__bar {
            flex: 1; height: 6px; background: #f3f4f6;
            border-radius: 100px; overflow: hidden;
        }
        .rk-route-item__bar-fill {
            height: 100%; background: #ef5050;
            border-radius: 100px; transition: width 0.4s ease;
        }
        .rk-route-item__pct {
            font-size: 11px; font-weight: 700; color: #ef5050;
            min-width: 32px; text-align: end;
        }
        .rk-empty {
            color: #9ca3af; text-align: center; padding: 32px 0; font-size: 13px;
        }

        /* ==================== ROW 4: OPERATIONS STRIP ==================== */
        .rk-ops-row {
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .rk-ops-chip {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 16px; border-radius: 10px;
            background: #f9fafb; border: 1px solid #f3f4f6;
            text-decoration: none; transition: all 150ms ease;
            flex: 1; min-width: 130px; justify-content: center;
        }
        .rk-ops-chip:hover {
            background: #fff; border-color: #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .rk-ops-chip:hover i { color: #ef5050; }
        .rk-ops-chip i {
            font-size: 20px; color: #9ca3af; transition: color 150ms ease;
        }
        .rk-ops-chip strong {
            font-size: 18px; font-weight: 800; color: #111827;
            letter-spacing: -0.01em;
        }
        .rk-ops-chip span {
            font-size: 11px; font-weight: 500; color: #9ca3af;
        }

        /* --- Override default .card --- */
        .rk-dashboard .card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* --- ApexCharts --- */
        .apexcharts-menu {
            min-width: 110px !important; background: #fff !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
            border-radius: 8px !important; overflow: hidden !important;
        }
        .apexcharts-menu-item { color: #374151 !important; font-size: 12px !important; }
        .apexcharts-menu-item:hover { background: #f9fafb !important; }
        .apexcharts-tooltip {
            background: #fff !important; border: 1px solid #e5e7eb !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important; border-radius: 10px !important;
        }
        .apexcharts-tooltip-title {
            background: #f9fafb !important; border-bottom: 1px solid #e5e7eb !important;
            color: #111827 !important; font-weight: 600 !important;
        }
        .apexcharts-tooltip-text-y-value { font-weight: 700 !important; }
        .apexcharts-xaxistooltip {
            background: #fff !important; border: 1px solid #e5e7eb !important;
            border-radius: 6px !important; color: #111827 !important;
        }
        .apexcharts-xaxistooltip::before { border-bottom-color: #e5e7eb !important; }
        .apexcharts-xaxistooltip::after { border-bottom-color: #fff !important; }
        .apexcharts-toolbar .apexcharts-menu-icon:hover svg,
        .apexcharts-toolbar .apexcharts-download-icon:hover svg { fill: #ef5050; }

        /* --- DateRangePicker --- */
        .daterangepicker {
            background: #fff !important; border: 1px solid #e5e7eb !important;
            box-shadow: 0 16px 32px rgba(0,0,0,0.12) !important;
            border-radius: 12px !important;
            font-family: 'IBM Plex Sans Arabic', 'Poppins', sans-serif !important;
        }
        .daterangepicker::before { border-bottom-color: #e5e7eb !important; }
        .daterangepicker::after { border-bottom-color: #fff !important; }
        .daterangepicker .calendar-table { background: transparent !important; border: none !important; }
        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background-color: #ef5050 !important; border-color: #ef5050 !important;
            color: #fff !important; border-radius: 6px;
        }
        .daterangepicker td.in-range { background-color: rgba(239,80,80,0.06) !important; color: #111827 !important; }
        .daterangepicker .btn-primary { background: #ef5050 !important; border-color: #ef5050 !important; border-radius: 8px !important; }
        .daterangepicker .btn-primary:hover { background: #dc4545 !important; }
        .daterangepicker .ranges li { border-radius: 6px !important; }
        .daterangepicker .ranges li.active { background: #ef5050 !important; color: #fff !important; }
        .daterangepicker .ranges li:hover { background: #f9fafb !important; }
        .daterangepicker .ranges li.active:hover { background: #dc4545 !important; }
        .daterangepicker th { color: #111827 !important; font-weight: 600 !important; }
        .daterangepicker td { color: #374151 !important; border-radius: 4px !important; }
        .daterangepicker td.off { color: #d1d5db !important; }
        .daterangepicker td:hover { background: #f9fafb !important; }
        .daterangepicker select.monthselect,
        .daterangepicker select.yearselect {
            border: 1px solid #e5e7eb !important; border-radius: 6px !important;
            color: #111827 !important; font-size: 12px !important;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 1199px) {
            .rk-split { grid-template-columns: 1fr; }
        }
        @media (max-width: 991px) {
            .rk-today-strip__grid { grid-template-columns: repeat(2, 1fr); }
            .rk-mini-stat:nth-child(2) { border-right: none; border-left: none; }
            [dir="rtl"] .rk-mini-stat:nth-child(2) { border-left: none; border-right: none; }
            .rk-mini-stat { border-bottom: 1px solid #f3f4f6; }
            .rk-mini-stat:nth-last-child(-n+2) { border-bottom: none; }
            .rk-kpi-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 767px) {
            .rk-mini-stat__value { font-size: 18px; }
            .rk-kpi-card__amount { font-size: 20px; }
            .rk-card { padding: 16px; }
            .rk-ops-chip { min-width: 110px; }
        }
        @media (max-width: 575px) {
            .rk-today-strip__grid { grid-template-columns: 1fr; }
            .rk-mini-stat { border-right: none !important; border-left: none !important; border-bottom: 1px solid #f3f4f6; }
            .rk-mini-stat:last-child { border-bottom: none; }
            .rk-kpi-row { grid-template-columns: 1fr; }
            .rk-today-strip__header { flex-direction: column; align-items: flex-start; }
            .rk-ops-chip { flex: unset; min-width: calc(50% - 4px); }
        }
    </style>
@endpush
