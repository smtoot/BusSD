@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Route')</label>
                                <select name="route_id" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}" @selected(request()->route_id == $route->id)>
                                            {{ $route->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Trip')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="trip_id">
                                    <option value="">@lang('All')</option>
                                    @foreach ($trips as $trip)
                                        <option value="{{ $trip->id }}" @selected(request()->trip_id == $trip->id)>
                                            {{ $trip->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Booking Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off"
                                    value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date of Journey')</label>
                                <input name="date_of_journey" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off"
                                    value="{{ request()->date_of_journey }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45">
                                    <i class="fas fa-filter"></i>
                                    @lang('Filter')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Booking Time')</th>
                                    <th>@lang('Date Of Journey')</th>
                                    <th>@lang('Ticket ID')</th>
                                    <th>@lang('Booked By')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales ?? [] as $sale)
                                    <tr>
                                        <td>{{ showDateTime($sale->created_at, 'M d, Y h:i A') }}</td>
                                        <td>{{ showDateTime($sale->date_of_journey, 'M d, Y') }}</td>
                                        <td>{{ sprintf('%06d', $sale->id) }}</td>
                                        <td>
                                            @if($sale->passenger_id)
                                                <span class="badge badge--info">@lang('App Booking')</span>
                                            @else
                                                {{ $sale->counterManager->fullname ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>{{ $sale->trip->title }}</td>
                                        <td>
                                            {{ gs('cur_sym') }}
                                            {{ getAmount($sale->ticket_count * $sale->price) }}
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.report.sale.details', $sale->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-desktop"></i> @lang('Details')
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
                @if (@$sales->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$sales) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'MMM D, YYYY',
                    applyLabel: '{{ __("Apply") }}',
                    cancelLabel: '{{ __("Cancel") }}',
                    fromLabel: '{{ __("From") }}',
                    toLabel: '{{ __("To") }}',
                    customRangeLabel: '{{ __("Custom Range") }}',
                    daysOfWeek: [
                        '{{ __("Su") }}', '{{ __("Mo") }}', '{{ __("Tu") }}', '{{ __("We") }}', '{{ __("Th") }}', '{{ __("Fr") }}', '{{ __("Sa") }}'
                    ],
                    monthNames: [
                        '{{ __("January") }}', '{{ __("February") }}', '{{ __("March") }}', '{{ __("April") }}', '{{ __("May") }}', '{{ __("June") }}',
                        '{{ __("July") }}', '{{ __("August") }}', '{{ __("September") }}', '{{ __("October") }}', '{{ __("November") }}', '{{ __("December") }}'
                    ],
                    firstDay: 1
                },
                showDropdowns: true,
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
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }

            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));

            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
        })(jQuery)
    </script>
@endpush
