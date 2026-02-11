@extends('co_owner.layouts.app')
@section('panel')
    <div class="row gy-4">
        @if (count($widget['active_packages']) == 0)
            <div class="col-lg-12">
                <div class="alert border border--danger bg--white" role="alert">
                    <div class="alert__icon bg--danger"><i class="far fa-bell"></i></div>
                    <p class="alert__message"> @lang('You\'ve no active package. Please contact with your admin.')
                    </p>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
            </div>
        @endif
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.vehicle.index') }}" icon="las la-bus" title="Total Vehicles"
                value="{{ $widget['total_bus'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.driver.index') }}" icon="las la-user-astronaut"
                title="Total Drivers" value="{{ $widget['total_driver'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.supervisor.index') }}" icon="las la-user-tie"
                title="Total Supervisors" value="{{ $widget['total_supervisor'] }}" bg="1" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="" icon="las la-users" title="Total Co-Admin"
                value="{{ $widget['total_coAdmin'] }}" bg="17" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.trip.route.index') }}" icon="las la-route"
                title="Total Routes" value="{{ $widget['total_route'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.trip.index') }}" icon="las la-radiation-alt"
                title="Total Trips" value="{{ $widget['total_trip'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.counter.index') }}" icon="las la-landmark"
                title="Total Counters" value="{{ $widget['total_counter'] }}" bg="1" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('co-owner.counter.manager.index') }}" icon="las la-user-alt"
                title="Counter Managers" value="{{ $widget['total_counter_manager'] }}" bg="17" />
        </div>
    </div>

    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Sales Report')</h5>
                        <div id="dwDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="dwChartArea"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Sales Report For Routes')</h5>
                    </div>
                    <div id="apex-circle-chart"> </div>
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
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }

        let dwChart = barChart(
            document.querySelector("#dwChartArea"),
            @json(__(gs('cur_text'))),
            [{
                name: 'Sold',
                data: []
            }],
            [],
        );

        const depositWithdrawChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }
            const url = @json(route('co-owner.chart.sales'));
            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span',
            start, end));

        changeDatePickerText('#dwDatePicker span', start, end);

        depositWithdrawChart(start, end);

        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositWithdrawChart(picker.startDate, picker
            .endDate));

        // apex-circle-chart js
        var options = {
            series: @json($bookedTicket['sale_price']->flatten()),
            chart: {
                height: 330,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    offsetY: 0,
                    startAngle: 0,
                    endAngle: 270,
                    hollow: {
                        margin: 5,
                        size: '30%',
                        background: 'transparent',
                        image: undefined,
                    },
                    dataLabels: {
                        name: {
                            show: true,
                        },
                        value: {
                            show: true,
                            formatter: function(val) {
                                return `{{ gs('cur_sym') }}${val}`
                            }
                        }
                    }
                }
            },
            labels: @json($bookedTicket['route_name']->flatten()),
            legend: {
                show: true,
                floating: true,
                fontSize: '16px',
                position: 'left',
                offsetX: -25,
                offsetY: -10,
                labels: {
                    useSeriesColors: true,
                },
                markers: {
                    size: 0
                },
                formatter: function(seriesName, opts) {
                    return seriesName + ":  {{ gs('cur_sym') }}" + opts.w.globals
                        .series[opts.seriesIndex]
                },
                itemMargin: {
                    vertical: 3
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        show: false
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#apex-circle-chart"), options);
        chart.render();
    </script>
@endpush

@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }
    </style>
@endpush
