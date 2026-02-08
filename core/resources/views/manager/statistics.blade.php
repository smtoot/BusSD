@extends('manager.layouts.app')
@section('panel')
    <h4 class="mb-3">@lang('Sale Amount')</h4>
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.todays') }}" icon="las las la-calendar-day"
                title="TODAY'S"
                value="{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}{{ showAmount($dailySale->total_sales, currencyFormat: false) }}"
                bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.all') }}" icon="las la-calendar"
                title="THIS MONTH"
                value="{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}{{ showAmount(collect(array_values($monthlySale))->sum(), currencyFormat: false) }}"
                bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.all') }}" icon="las la-calendar-week"
                title="THIS YEAR"
                value="{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}{{ showAmount(collect(array_values($yearlySale))->sum(), currencyFormat: false) }}"
                bg="1" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.all') }}" icon="las la-calendar-check"
                title="ALL TIME"
                value="{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}{{ showAmount(@$allSale->total_sales, currencyFormat: false) }}"
                bg="3" />
        </div>
    </div>
    <h4 class="my-3">@lang('Ticket Count')</h4>
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.todays') }}" icon="las las la-calendar-day"
                title="TODAY'S" value="{{ $dailySale->total_ticket ?? 0 }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.all') }}" icon="las la-calendar"
                title="THIS MONTH" value="{{ $monthlyTicketCount ?? 0 }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.all') }}" icon="las la-calendar-week"
                title="THIS YEAR" value="{{ $yearlyTicketCount ?? 0 }}" bg="1" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('manager.sold.tickets.all') }}" icon="las la-calendar-check"
                title="ALL TIME" value="{{ $allSale->total_ticket ?? 0 }}" bg="3" />
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"> @lang('Sales Report For ' . date('F'))</h5>
                    <div id="apex-line"> </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Sales Report For ' . date('Y'))</h5>
                    <div id="apex-bar-chart"> </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
        // apex-line chart
        var options = {
            chart: {
                height: 420,
                type: "area",
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    enabledSeries: [0],
                    top: -2,
                    left: 0,
                    blur: 10,
                    opacity: 0.08
                },
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
            },
            dataLabels: {
                enabled: false,
                formatter: function(val, opt) {
                    return `{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}${val}`
                },
                offsetX: 0,
            },
            markers: {
                colors: ['#F44336', '#E91E63', '#9C27B0']
            },
            series: [{
                name: "Total Sale",
                data: @json(array_values($monthlySale)),
            }, ],
            tooltip: {
                y: {
                    formatter: function(val, opt) {
                        return `{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}${val}`
                    },
                }
            },
            offsetX: 0,
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                },
                colors: ['#1E9FF2', '#101536', '#7367F0']
            },
            colors: ['#101536'],
            xaxis: {
                name: 'Date',
                categories: @json(array_keys($monthlySale)),
            },
            yaxis: {
                title: {
                    text: "Amount in {{ @$owner->general_settings->cur_text ?? gs('cur_text') }}",
                    style: {
                        color: '#7c97bb',
                        fontWeight: '400',
                    }
                }
            },
            grid: {
                padding: {
                    left: 5,
                    right: 5
                },
                xaxis: {
                    type: 'datetime',
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#apex-line"), options);
        chart.render();

        // apex-bar-chart js
        var options = {
            series: [{
                name: 'Sale',
                data: @json(array_values($yearlySale))
            }],
            chart: {
                type: 'bar',
                height: 420,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '5%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json(array_keys($yearlySale)),
            },
            yaxis: {
                title: {
                    text: "Amount in {{ @$owner->general_settings->cur_text ?? gs('cur_text') }}",
                    style: {
                        color: '#7c97bb',
                        fontWeight: '400',
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}" + val
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#apex-bar-chart"), options);
        chart.render();
    </script>
@endpush
