@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4 align-items-end">
                            <div class="flex-grow-1">
                                <label>@lang('Select Date Range (Journey Date)')</label>
                                <input name="date_of_journey" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off"
                                    value="{{ request()->date_of_journey }}" required>
                            </div>
                            <div class="flex-grow-1">
                                <button class="btn btn--primary w-100 h-45">
                                    <i class="fas fa-filter"></i>
                                    @lang('Generate Report')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(request()->date_of_journey)
                <div class="card b-radius--10 ">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two custom-data-table">
                                <thead>
                                    <tr>
                                        <th>@lang('Date Of Journey')</th>
                                        <th>@lang('Ticket ID')</th>
                                        <th>@lang('Trip')</th>
                                        <th>@lang('Type')</th>
                                        <th>@lang('Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $sale)
                                        <tr>
                                            <td>{{ showDateTime($sale->date_of_journey, 'M d, Y') }}</td>
                                            <td>{{ $sale->trx }}</td>
                                            <td>{{ $sale->trip->title }}</td>
                                            <td>
                                                @if($sale->passenger_id)
                                                    <span class="badge badge--info">@lang('App')</span>
                                                @else
                                                    <span class="badge badge--dark">@lang('Counter')</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ gs('cur_sym') }}{{ getAmount($sale->ticket_count * $sale->price) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No sales found for this period') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($sales->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($sales) }}
                        </div>
                    @endif
                </div>
            @else
                <div class="alert alert-info text-center b-radius--10">
                    <i class="las la-info-circle"></i> @lang('Please select a date range to generate the periodic report.')
                </div>
            @endif
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
                    '{{ __("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                    '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                    '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('.date-range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('.date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        })(jQuery)
    </script>
@endpush
