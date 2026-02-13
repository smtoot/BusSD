@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.all') }}" icon="las la-users" title="Total Owners"
                value="{{ $widget['total_owners'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.passengers.index') }}" icon="las la-user-tag" title="Total Passengers"
                value="{{ $widget['total_passengers'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.trips.index') }}" icon="las la-bus-alt"
                title="Active Trips" value="{{ $widget['active_trips'] }}" bg="danger" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.bookings.index') }}" icon="las la-ticket-alt"
                title="Total Bookings" value="{{ $widget['total_bookings'] }}" bg="warning" />
        </div>
    </div>

    <!-- B2C Operations Row -->
    <div class="row mt-4 gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.settlements.index') }}" icon="las la-hand-holding-usd" title="Pending Settlements"
                value="{{ showAmount($widget['pending_settlement_sum']) }}" bg="info" type="2" icon_style="false" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.settlements.index') }}" icon="las la-clock" title="Settlements Queue"
                value="{{ $widget['pending_settlements'] }}" bg="warning" type="2" icon_style="false" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.users.all') }}?verified=pending" icon="las la-user-check" title="Pending Verifications"
                value="{{ $widget['pending_verifications'] }}" bg="danger" type="2" icon_style="false" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.bookings.index') }}" icon="las la-lock" title="Active Seat Locks"
                value="{{ $widget['active_seat_locks'] }}" bg="primary" type="2" icon_style="false" />
        </div>
    </div>

    <div class="row mt-2 gy-4">
        <div class="col-xxl-6">
            <div class="card box-shadow3 h-100">
                <div class="card-body">
                    <h5 class="card-title">@lang('Booking Statistics')</h5>
                    <div class="widget-card-wrapper">
                        <div class="widget-card bg--success">
                            <a href="{{ route('admin.bookings.b2c') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $widget['b2c_bookings'] }}</h6>
                                    <p class="widget-card-title">@lang('App Bookings')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>
                        <div class="widget-card bg--info">
                            <a href="{{ route('admin.bookings.counter') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $widget['counter_bookings'] }}</h6>
                                    <p class="widget-card-title">@lang('Counter Bookings')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>
                        <div class="widget-card bg--primary">
                            <a href="{{ route('admin.routes.index') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-route"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $widget['total_routes'] }}</h6>
                                    <p class="widget-card-title">@lang('Total Routes')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>
                        <div class="widget-card bg--warning">
                            <a href="{{ route('admin.counters.index') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $widget['total_counters'] }}</h6>
                                    <p class="widget-card-title">@lang('Total Counters')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6">
            <div class="card box-shadow3 h-100">
                <div class="card-body">
                    <h5 class="card-title">@lang('Aggregator Revenue')</h5>
                    <div class="widget-card-wrapper">

                        <div class="widget-card bg--success">
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($widget['total_commissions']) }}</h6>
                                    <p class="widget-card-title">@lang('Total B2C Commission')</p>
                                </div>
                            </div>
                        </div>

                        <div class="widget-card bg--primary">
                            <a href="{{ route('admin.deposit.list') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_amount']) }}</h6>
                                    <p class="widget-card-title">@lang('Total Payments')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--warning">
                            <a href="{{ route('admin.deposit.pending') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $deposit['total_deposit_pending'] }}</h6>
                                    <p class="widget-card-title">@lang('Pending Payments')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--danger">
                            <a href="{{ route('admin.ticket.index') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ @$widget['tickets'] }}</h6>
                                    <p class="widget-card-title">@lang('Support Tickets')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Booking Comparison (B2C vs Counter)')</h5>
                        <div id="bookingDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="bookingChartArea"> </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Payment Report')</h5>
                        <div id="dwDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="dwChartArea"> </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <h5 class="mb-3">@lang('Latest Owners')</h5>
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestOwners as $owner)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $owner->id) }}"><span>@</span>{{ $owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $owner->mobileNumber }}</td>
                                        <td>{{ $owner->email }}</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.users.detail', $owner->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <h5 class="mb-3">@lang('Latest Sold Packages')</h5>
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Package')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSales as $sales)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$sales->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', @$sales->owner_id) }}">
                                                    <span>@</span>{{ $sales->owner->username }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>{{ __($sales->package->name) }}</td>
                                        <td>{{ showAmount($sales->price) }}</td>
                                        <td>
                                            <div class="button-group">
                                                <a href="{{ route('admin.report.sales.history') }}?search={{ $sales->owner->username }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop text--shadow"></i> @lang('details')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No Sell Yet')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-12 mb-30">
            <h5 class="mb-3">@lang('Recent App Bookings')</h5>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('PNR')</th>
                                    <th>@lang('Trip / Operator')</th>
                                    <th>@lang('Fare')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Booked At')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestB2CBookings as $booking)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$booking->passenger->fullname }}</span>
                                            <br>
                                            <span class="small">+{{ @$booking->passenger->mobile }}</span>
                                        </td>
                                        <td><span class="fw-bold">{{ $booking->pnr }}</span></td>
                                        <td>
                                            <span class="fw-bold">{{ @$booking->trip->route->name }}</span>
                                            <br>
                                            <span class="small text--muted">{{ @$booking->trip->owner->fullname }}</span>
                                        </td>
                                        <td>{{ showAmount($booking->ticket_price) }}</td>
                                        <td>
                                            @php echo $booking->statusBadge; @endphp
                                        </td>
                                        <td>{{ showDateTime($booking->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No B2C bookings found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

        // Booking Chart
        let bookingChartArea = lineChart(
            document.querySelector("#bookingChartArea"),
            'Count',
            [{
                name: 'B2C',
                data: []
            }, {
                name: 'Counter',
                data: []
            }],
            [],
        );

        const fetchBookingChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }
            const url = @json(route('admin.chart.booking'));
            $.get(url, data, function(data, status) {
                if (status == 'success') {
                    bookingChartArea.updateSeries(data.data);
                    bookingChartArea.updateOptions({
                        xaxis: {
                            categories: data.created_on,
                        },
                        colors: ['#ef5050', '#1f2937']
                    });
                }
            });
        }

        $('#bookingDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#bookingDatePicker span', start, end));
        changeDatePickerText('#bookingDatePicker span', start, end);
        fetchBookingChart(start, end);
        $('#bookingDatePicker').on('apply.daterangepicker', (event, picker) => fetchBookingChart(picker.startDate, picker.endDate));


        // Payment Chart
        let dwChart = barChart(
            document.querySelector("#dwChartArea"),
            @json(__(gs('cur_text'))),
            [{
                name: 'Deposited',
                data: []
            }],
            [],
        );

        const depositChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }
            const url = @json(route('admin.chart.deposit'));
            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            },
                            colors: ['#ef5050']
                        });
                    }
                }
            );
        }

        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span',
            start, end));

        changeDatePickerText('#dwDatePicker span', start, end);

        depositChart(start, end);

        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositChart(picker.startDate, picker.endDate));

    </script>
@endpush

@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }
    </style>
@endpush
