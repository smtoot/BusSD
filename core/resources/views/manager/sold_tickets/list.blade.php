@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Booking Time')</th>
                                    <th>@lang('Date of Journey')</th>
                                    <th>@lang('Ticket ID')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Ticket Count')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($soldTickets as $soldTicket)
                                    @php
                                        $sourceDestination = getStoppageInfo($soldTicket->source_destination)->pluck(
                                            'name',
                                        );
                                        $source = $sourceDestination[0];
                                        $destination = $sourceDestination[1];
                                    @endphp
                                    <tr>
                                        <td>{{ showDateTime($soldTicket->created_at) }}</td>
                                        <td>{{ showDateTime($soldTicket->date_of_journey, 'M d, Y') }}</td>
                                        <td>{{ sprintf('%06d', $soldTicket->id) }}</td>
                                        <td>{{ __($soldTicket->trip->title) }}</td>
                                        <td>{{ $soldTicket->ticket_count }}</td>
                                        <td>
                                            {{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}
                                            {{ showAmount($soldTicket->ticket_count * $soldTicket->price, currencyFormat: false) }}
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-outline--primary btn-sm detailBtn"
                                                    data-ticket="{{ $soldTicket }}" data-from="{{ __($source) }}"
                                                    data-to="{{ __($destination) }}">
                                                    <i class="la la-desktop"></i> @lang('Details')
                                                </button>
                                                <a href="{{ route('manager.sell.ticket.print', $soldTicket->id) }}"
                                                    class="btn btn-outline--warning btn-sm" target="_blank">
                                                    <i class="la la-print"></i> @lang('Print')
                                                </a>
                                                @if ($soldTicket->status == Status::DISABLE)
                                                    <button class="btn btn-outline--success btn-sm confirmationBtn"
                                                        data-action="{{ route('manager.sold.tickets.cancel', $soldTicket->id) }}"
                                                        data-question="@lang('Are you sure you sure to rebook?')">
                                                        <i class="la la-reply"></i> @lang('Rebook')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--danger btn-sm confirmationBtn"
                                                        data-action="{{ route('manager.sold.tickets.cancel', $soldTicket->id) }}"
                                                        data-question="@lang('Are you sure you sure to cancel this booking?')">
                                                        <i class="la la-times"></i> @lang('Cancel Booking')
                                                    </button>
                                                @endif
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
                @if ($soldTickets->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($soldTickets) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ticket Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold trip-title"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Date of Journey')</span>
                            <span class="date-of-journey"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Pickup Point')</span>
                            <span class="from"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Dropping Point')</span>
                            <span class="to"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Ticket Count')</span>
                            <span class="ticket-count"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Total Price')</span>
                            <span class="ticket-price"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Seat Number')</span>
                            <span class="seat-number"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-center">
                            <span class="fw-bold text--cyan">@lang('Passenger Details')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Name')</span>
                            <span class="passenger-name"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Mobile Number')</span>
                            <span class="passenger-mobile"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Email')</span>
                            <span class="passenger-email"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between border-0">
                            <span class="fw-bold">@lang('Gender')</span>
                            <span class="passenger-gender"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filterModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text--black">@lang('Filter Sold Tickets')</h5>
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="get">
                    <div class="modal-body">
                        <div class="row align-items-top">
                            <div class="col-8">
                                <small class="text--danger my-3">
                                    <i class="la la-info-circle"></i>
                                    @lang('Select at least 1 or more fields as you want and keep rest of the fields empty.')
                                </small>
                            </div>
                            <div class="col-4 text-end">
                                <button type="button" class="btn btn-outline--warning btn-sm resetBtn">
                                    <i class="las la-sync"></i> @lang('Reset')
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Route')</label>
                            <select class="form-control select2" name="route_id">
                                <option value="" selected>@lang('Select One')</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Trip')</label>
                            <select class="form-control select2" name="trip_id">
                                <option value="" selected>@lang('Select One')</option>
                                @foreach ($trips as $trip)
                                    <option value="{{ $trip->id }}">{{ $trip->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Booking Date')</label>
                            <input type="text" name="booking_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>@lang('Date of Journey')</label>
                            <input type="text" name="date_of_journey" class="datepicker-here form-control date-range"
                                value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn w-100 h-45 btn--primary">@lang('Filter')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Ticket Id" />

    @if (!request()->routeIs('manager.sold.tickets.todays'))
        <button type="button" class="btn btn-outline--warning filterBtn">
            <i class="las la-filter"></i> @lang('Filter')
        </button>
    @endif
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let request = @json(request()->all());

            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var ticket = $(this).data('ticket');
                modal.find('.trip-title').text(ticket.trip.title);
                modal.find('.date-of-journey').text(ticket.date_of_journey);
                modal.find('.from').text($(this).data('from'));
                modal.find('.to').text($(this).data('to'));
                modal.find('.ticket-count').text((ticket.ticket_count));
                modal.find('.ticket-price').text((ticket.ticket_count * ticket.price));
                modal.find('.seat-number').text(ticket.seats);
                modal.find('.passenger-name').text((ticket.passenger_details.name));
                modal.find('.passenger-mobile').text((ticket.passenger_details.mobile_number));
                modal.find('.passenger-email').text((ticket.passenger_details.email));
                modal.find('.passenger-gender').text((ticket.passenger_details.gender == 1 ?
                    "{{ trans('Male') }}" : ticket.passenger_details.gender == 2 ?
                    "{{ trans('Female') }}" : "{{ trans('Others') }}"));
                modal.modal('show');
            });

            $('input[name="date_of_journey"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 2000,
                maxYear: parseInt(moment().format('YYYY'), 10),
                locale: {
                    format: 'Y-M-D',
                }
            });

            $('input[name="booking_date"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 2000,
                maxYear: parseInt(moment().format('YYYY'), 10),
                locale: {
                    format: 'Y-M-D',
                }
            });

            $('.filterBtn').on('click', function() {
                let modal = $('#filterModal');
                modal.find('select[name=route_id]').val(request.route_id ?? '').change();
                modal.find('select[name=trip_id]').val(request.trip_id ?? '').change();
                $('input[name="date_of_journey"]').val(request.date_of_journey ?? '');
                $('input[name="booking_date"]').val(request.created_at ?? '');
                modal.modal('show');
            });

            $('.resetBtn').on('click', function() {
                $('select[name=route_id]').val('').change();
                $('select[name=trip_id]').val('').change();
                $('input[name="date_of_journey"]').val('');
                $('input[name="booking_date"]').val('');
            });

            $(document).on('click', '.re-booking', function() {
                var modal = $('#rebookModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
