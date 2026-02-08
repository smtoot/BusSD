@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <form action="{{ route('manager.sell.book.booked', $trip->id) }}" class="mt-2" id="booking-form"
                    method="POST">
                    @csrf
                    <div class="card-body">

                        <input type="hidden" name="price" />
                        <input type="hidden" name="seat_number" />

                        <h5 class=" text-center">@lang('Passenger Details')</h5>

                        <div class="form-group">
                            <label>@lang('Date of Journey')</label>
                            <input name="date_of_journey" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Pickup Point')</label>
                            <select class="select2 form-control" name="pick_up_point" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($stoppages as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Dropping Point')</label>
                            <select class="select2 form-control" name="dropping_point" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($stoppages as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="booked-seat-details my-3 d-none">
                            <label>@lang('Selected Seats')</label>
                            <ul class="list-group seat-details-animate">
                                <li class="list-group-item bg--primary">@lang('Seat Details')</li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Mobile Number')</label>
                            <input type="text" name="mobile_number" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Email')</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>@lang('Gender')</label>
                            <div class="d-flex align-items-center flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="male"
                                        value="1">
                                    <label class="form-check-label" for="male">@lang('Male')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="female"
                                        value="2">
                                    <label class="form-check-label" for="female">@lang('Female')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="other"
                                        value="0">
                                    <label class="form-check-label" for="other">@lang('Others')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Book')</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-7 col-lg-12 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="p-2 text-center mb-2 border-bottom">@lang('Select Seats')</h5>
                    <div class="seat-plan-info">
                        <div class="seat-plan-info-single">
                            <span class="color-box available"></span>
                            <span class="caption">@lang('Available')</span>
                        </div>
                        <div class="seat-plan-info-single">
                            <span class="color-box selected"></span>
                            <span class="caption">@lang('Selected')</span>
                        </div>
                        <div class="seat-plan-info-single">
                            <span class="color-box booked"></span>
                            <span class="caption"> @lang('Booked') : @lang('Male')</span>
                        </div>
                        <div class="seat-plan-info-single">
                            <span class="color-box booked-female"></span>
                            <span class="caption"> @lang('Booked') : @lang('Female')</span>
                        </div>
                        <div class="seat-plan-info-single">
                            <span class="color-box booked-others"></span>
                            <span class="caption"> @lang('Booked') : @lang('Others')</span>
                        </div>
                    </div>
                    @foreach ($trip->fleetType->seats as $deck => $seat)
                        <div class="seat-plan-wrapper">
                            @if ($deck == 1)
                                <div class="diver-seat text-end">
                                    <button type="button" class="seat-btn driver-seat" disabled>
                                        <i class="la la-radiation-alt"></i>
                                    </button>
                                </div>
                            @else
                                <div class="diver-seat text-end">
                                    <h5 class="mb-0">@lang('Deck'): {{ $deck }}</h5>
                                </div>
                            @endif
                            <div class="seat-plan">
                                @php
                                    $seatLayout = seatLayoutToArray($trip->fleetType->seatLayout->layout);
                                    $left = $seatLayout[0];
                                    $right = $seatLayout[1];
                                    $rowItem = $left + $right;
                                    $totalRow = floor($seat / $rowItem);

                                    if ($seat / $rowItem > 0 && $seat / $rowItem < 1) {
                                        $totalRow = 1;
                                        $seatCount = $seat;
                                    }
                                    $lastRowSeat = $seat - $totalRow * $rowItem;
                                    $chr = 'A';
                                @endphp
                                @for ($i = 1; $i <= $totalRow; $i++)
                                    @php
                                        $seatNumber = $chr;
                                        $chr++;
                                    @endphp
                                    <div class="single-row">
                                        <div class="left">
                                            @for ($l = 1; $l <= $left; $l++)
                                                <button type="button" class="seat-btn"
                                                    value="{{ $deck }} - {{ $seatNumber }}{{ $l }}">{{ $seatNumber }}{{ $l }}</i></button>
                                            @endfor
                                        </div>
                                        <div class="right">
                                            @for ($r = 0; $r < $right; $r++)
                                                <button type="button" class="seat-btn"
                                                    value="{{ $deck }} - {{ $seatNumber }}{{ $l + $r }}">{{ $seatNumber }}{{ $l + $r }}</i></button>
                                            @endfor
                                        </div>
                                    </div>
                                @endfor

                                @if ($lastRowSeat == 1)
                                    @php @$seatNumber++ @endphp
                                    <div class="single-row d-flex">
                                        <button type="button" value="{{ $deck }} - {{ $seatNumber }}1"
                                            class="seat-btn">{{ $seatNumber }}1</i></button>
                                    </div>
                                @endif
                                @if ($lastRowSeat > 1)
                                    @php $seatNumber++ @endphp
                                    <div class="single-row">
                                        @for ($l = 1; $l <= $lastRowSeat; $l++)
                                            <button type="button" class="seat-btn"
                                                value="{{ $deck }} - {{ $seatNumber }}{{ $l }}">{{ $seatNumber }}{{ $l }}</i></button>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modelId">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ticket Prices: Stoppage to Stoppage')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($ticketPrices as $item)
                            @if ($item->price > 0)
                                @php
                                    $stoppages = getStoppageInfo($item->source_destination);
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $stoppages[0]->name }} - {{ $stoppages[1]->name }}
                                    <span class="font-weight-bolder">
                                        {{ showAmount($item->price, currencyFormat: false) }}{{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}
                                    </span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <i class="las la-times-circle f-size--100 text--danger mb-15"></i>
                    <h5 class="text--danger mb-15 error-message"></h5>
                    <button type="button" class="btn btn--danger closeModal">
                        @lang('Continue')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookConfirm">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirm Booking')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure to book these seats?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--success" id="confirm-book">@lang('Yes')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="helpModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('How to book a ticket?')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text--info mb-2">@lang('Check off days before booking')</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 1') <span>@lang('Select date of journey')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 2') <span>@lang('Select pickup point')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 3') <span>@lang('Select dropping point')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 4') <span>@lang('Select one or more seats')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 5') <span>@lang('Fill up passenger\'s details')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 6') <span>@lang('Click or tap on book button')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Step 7') <span> <i class="la la-print"></i> @lang('Ticket')</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn--primary ticketPriceBtn">
        @lang('Ticket Price List') <i class="lar la-list-alt"></i>
    </button>

    <button type="button" class="btn btn--info btn-sm helpBtn">
        @lang('Help') <i class="las la-question-circle"></i>
    </button>
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
        'use strict';

        (function($) {
            let minYear = parseInt(moment().format('YYYY'), 10);

            $('input[name="date_of_journey"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: minYear,
                maxYear: minYear + 20
            });

            $('.closeModal').on('click', function() {
                $('#alertModal').modal('hide');
            });

            $('.ticketPriceBtn').on('click', function() {
                $('#modelId').modal('show');
            });

            $('.helpBtn').on('click', function() {
                $('#helpModal').modal('show');
            });

            var booked_seats = JSON.parse('@php echo json_encode($bookedTickets) @endphp');

            $.each(booked_seats, function(i, v) {
                $.each(v.seats, function(index, val) {
                    var title = `${v.passenger_details.from} To ${v.passenger_details.to}`;
                    if (v.passenger_details.gender == 1)
                        $(`.seat-btn[value="${val}"]`).addClass('booked').attr('disabled', 'disabled')
                        .attr('title', `${title}`);
                    else if (v.passenger_details.gender == 2)
                        $(`.seat-btn[value="${val}"]`).addClass('booked-female').attr('disabled',
                            'disabled').attr('title', `${title}`);
                    else if (v.passenger_details.gender == 0)
                        $(`.seat-btn[value="${val}"]`).addClass('booked-others').attr('disabled',
                            'disabled').attr('title', `${title}`);
                });
            });

            $(document).on('click', '.seat-btn', function() {
                var pick_up_point = $('select[name="pick_up_point"]').val();
                var dropping_point = $('select[name="dropping_point"]').val();
                if (pick_up_point && dropping_point) {
                    $(this).toggleClass('selected');
                    var seats = $('.selected').map((_, el) => el.value).get();
                    var price = $('input[name=price]').val();
                    var seat_data =
                        `<li class="list-group-item d-flex justify-content-between font-weight-bolder"> Deck - Seat <span>@lang('Price')</span></li>`;
                    if (seats.length > 0) {
                        $('.booked-seat-details').removeClass('d-none');
                        $.each(seats, function(i, v) {
                            seat_data +=
                                `<li class="list-group-item d-flex justify-content-between"> ${v} <span class="text--indigo">${price}</span></li>`;
                        });
                        seat_data +=
                            `<li class="list-group-item d-flex justify-content-between font-weight-bold"> Subtotal <span>${seats.length * price}</span></li>`;
                        $('.booked-seat-details .list-group').html(seat_data);
                    } else {
                        $('.booked-seat-details .list-group').html('');
                        $('.booked-seat-details').addClass('d-none');
                    }
                    $('input[name=seat_number]').val(seats);
                } else {
                    var modal = $('#alertModal');
                    modal.find('.error-message').text(
                        "{{ trans('Please select pickup point and dropping point before select any seat') }}"
                    );
                    modal.modal('show');
                }
            });

            $(document).on('change',
                'select[name="pick_up_point"], select[name="dropping_point"], input[name="date_of_journey"]',
                function(e) {
                    var date = $('input[name="date_of_journey"]').val();
                    var sourceId = $('select[name="pick_up_point"]').find("option:selected").val();
                    var destinationId = $('select[name="dropping_point"]').find("option:selected").val();

                    if (sourceId == destinationId && destinationId != '') {
                        var modal = $('#alertModal');
                        modal.find('.error-message').text(
                            "{{ trans('Source Point and Destination Point Must Not Be Same') }}");
                        modal.modal('show');
                        return false;
                    } else if (sourceId != destinationId) {

                        var routeId = '{{ $trip->route->id }}';
                        var fleetTypeId = '{{ $trip->fleetType->id }}';

                        if (sourceId && destinationId) {
                            getprice(routeId, fleetTypeId, sourceId, destinationId, date)
                        }
                    }
                });

            $('[name=date_of_journey]').on('change', function() {
                var date = $(this).val();
                $.ajax({
                    type: "get",
                    url: "{{ route('manager.sell.book.bydate', $trip->id) }}",
                    data: {
                        "date": date
                    },
                    success: function(response) {
                        $('.seat-btn').removeClass(['booked', 'booked-others', 'booked-female']);
                        $(`.seat-btn`).removeAttr('disabled');
                        $(`.seat-btn`).first().attr('disabled', 'disabled')
                        fillBookedSeats(response);
                    }
                });
            });

            function fillBookedSeats(response) {
                $.each(response.booked_seats, function(i, v) {
                    $.each(v.seats, function(index, val) {
                        var title = `${v.passenger_details.from} To ${v.passenger_details.to}`;
                        if (v.passenger_details.gender == 1)
                            $(`.seat-btn[value="${val}"]`).addClass('booked').attr('disabled',
                                'disabled').attr('title', `${title}`);
                        else if (v.passenger_details.gender == 2)
                            $(`.seat-btn[value="${val}"]`).addClass('booked-female').attr('disabled',
                                'disabled').attr('title', `${title}`);
                        else if (v.passenger_details.gender == 0)
                            $(`.seat-btn[value="${val}"]`).addClass('booked-others').attr('disabled',
                                'disabled').attr('title', `${title}`);
                    });
                });
            }

            $('#alertModal').on('hidden.bs.modal', event => {
                $('select[name="dropping_point"]').val('');
                $('.select2-basic').select2({
                    dropdownParent: $('.card-body form')
                });
            });

            $(document).on('submit', '#booking-form', function(e) {
                var modal = $('#bookConfirm');
                e.preventDefault();
                modal.modal('show');
            });

            $(document).on('click', '#confirm-book', function(e) {
                var modal = $('#bookConfirm');
                modal.modal('hide');
                document.getElementById("booking-form").submit();
            });

            function getprice(routeId, fleetTypeId, sourceId, destinationId, date) {
                var data = {
                    "trip_id": '{{ $trip->id }}',
                    "route_id": routeId,
                    "fleet_type_id": fleetTypeId,
                    "source_id": sourceId,
                    "destination_id": destinationId,
                    "date": date,
                }

                $.ajax({
                    type: "get",
                    url: "{{ route('manager.ticket.get-price') }}",
                    data: data,
                    success: function(response) {
                        if (response.error) {
                            var modal = $('#alertModal');
                            modal.find('.error-message').text(response.error);
                            modal.modal('show');
                        } else {
                            var stoppages = response.stoppages;
                            var req_source = response.req_source;
                            var req_destination = response.req_destination;

                            req_source = stoppages.indexOf(req_source.toString());
                            req_destination = stoppages.indexOf(req_destination.toString());

                            var title = ``;

                            if (response.reverse == true) {
                                $.each(response.bookedSeats, function(i, v) {
                                    var booked_source = v.pick_up_point; //Booked
                                    var booked_destination = v.dropping_point; //Booked

                                    booked_source = stoppages.indexOf(booked_source.toString());
                                    booked_destination = stoppages.indexOf(booked_destination
                                        .toString());

                                    if (req_destination >= booked_source || req_source <=
                                        booked_destination) {
                                        $.each(v.seats, function(index, val) {
                                            if (v.passenger_details.gender == 1)
                                                $(`.seat-btn[value="${val}"]`).removeClass(
                                                    'booked').removeAttr('disabled')
                                                .removeAttr('title');
                                            if (v.passenger_details.gender == 2)
                                                $(`.seat-btn[value="${val}"]`).removeClass(
                                                    'booked-female').removeAttr('disabled')
                                                .removeAttr('title');
                                            if (v.passenger_details.gender == 2)
                                                $(`.seat-btn[value="${val}"]`).removeClass(
                                                    'booked-others').removeAttr('disabled')
                                                .removeAttr('title');
                                        });
                                    } else {
                                        $.each(v.seats, function(index, val) {
                                            title =
                                                `${v.passenger_details.from} to ${v.passenger_details.to}`;

                                            if (v.passenger_details.gender == 1)
                                                $(`.seat-btn[value="${val}"]`).addClass(
                                                    'booked').attr('disabled', 'disabled')
                                                .attr('title', `${title}`);
                                            else if (v.passenger_details.gender == 2)
                                                $(`.seat-btn[value="${val}"]`).addClass(
                                                    'booked-female').attr('disabled',
                                                    'disabled').attr('title', `${title}`);
                                            else if (v.passenger_details.gender == 0)
                                                $(`.seat-btn[value="${val}"]`).addClass(
                                                    'booked-others').attr('disabled',
                                                    'disabled').attr('title', `${title}`);
                                        });
                                    }
                                });
                            } else {
                                $.each(response.bookedSeats, function(i, v) {
                                    var booked_source = v.pick_up_point; //Booked
                                    var booked_destination = v.dropping_point; //Booked

                                    booked_source = stoppages.indexOf(booked_source.toString());
                                    booked_destination = stoppages.indexOf(booked_destination
                                        .toString());

                                    if (req_destination <= booked_source || req_source >=
                                        booked_destination) {
                                        $.each(v.seats, function(index, val) {
                                            if (v.passenger_details.gender == 1)
                                                $(`.seat-btn[value="${val}"]`).removeClass(
                                                    'booked').removeAttr('disabled')
                                                .removeAttr('title');
                                            if (v.passenger_details.gender == 2)
                                                $(`.seat-btn[value="${val}"]`).removeClass(
                                                    'booked-female').removeAttr('disabled')
                                                .removeAttr('title');
                                            if (v.passenger_details.gender == 2)
                                                $(`.seat-btn[value="${val}"]`).removeClass(
                                                    'booked-others').removeAttr('disabled')
                                                .removeAttr('title');
                                        });
                                    } else {
                                        $.each(v.seats, function(index, val) {
                                            var title =
                                                `${v.passenger_details.from} to ${v.passenger_details.to}`;

                                            if (v.passenger_details.gender == 1)
                                                $(`.seat-btn[value="${val}"]`).addClass(
                                                    'booked').attr('disabled', 'disabled')
                                                .attr('title', `${title}`);
                                            else if (v.passenger_details.gender == 2)
                                                $(`.seat-btn[value="${val}"]`).addClass(
                                                    'booked-female').attr('disabled',
                                                    'disabled').attr('title', `${title}`);
                                            else if (v.passenger_details.gender == 0)
                                                $(`.seat-btn[value="${val}"]`).addClass(
                                                    'booked-others').attr('disabled',
                                                    'disabled').attr('title', `${title}`);
                                        });
                                    }
                                });
                            }

                            if (response.price.error) {
                                var modal = $('#alertModal');
                                modal.find('.error-message').text(response.price.error);
                                modal.modal('show');
                            } else {
                                $('input[name=price]').val(parseInt(response.price).toFixed(2));
                            }
                        }
                    }
                });
            }
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        /* seat-plan-wrapper css start */
        .seat-plan-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: -5px -10px;
        }

        .seat-plan-info-single {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            margin: 5px 10px;
        }

        .seat-plan-info-single .color-box {
            width: 15px;
            height: 15px;
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #7367f0;
        }

        .seat-plan-info-single .color-box.booked {
            background-color: #2b2149;
        }

        .seat-plan-info-single .color-box.booked-female {
            background-color: #b05eef;
            border-color: #b05eef;
        }

        .seat-plan-info-single .color-box.booked-others {
            background-color: #a22929;
            border-color: #a22929;
        }



        .seat-plan-info-single .color-box.selected {
            background-color: #7367f0;
        }

        .seat-plan-info-single .caption.caption {
            font-size: 13px;
            margin-left: 10px;
            font-weight: 500;
        }

        .seat-plan-wrapper {
            border: 1px solid #0064fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 30px;
            display: inline-block;
        }

        @media (max-width: 420px) {
            .seat-plan-wrapper {
                padding: 8px;
                display: block;
            }
        }

        .diver-seat .seat-btn {
            font-size: 24px;
        }

        @media (max-width: 450px) {
            .diver-seat .seat-btn {
                font-size: 18px;
            }

            .seat-plan-info {
                justify-content: flex-start;
            }
        }

        @media (max-width: 320px) {
            .diver-seat .seat-btn {
                font-size: 14px;
            }
        }

        .seat-plan {
            margin-top: 30px;
        }

        .seat-plan .single-row .left {
            display: inline-block;
            margin-right: 50px;
            text-align: left;

        }

        .seat-plan .single-row .right {
            display: inline-block;
            text-align: right;
        }

        .seat-btn {
            width: 50px;
            height: 40px;
            border: 1px solid #7367f0;
            background-color: #ffffff;
            font-size: 13px;
            margin: 4px 2px;
            border-radius: 3px;
            white-space: nowrap;
        }



        @media (max-width: 1350px) and (min-width: 1200px) {
            .seat-btn {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }
        }

        @media (max-width: 450px) {
            .seat-btn {
                width: 30px;
                height: 27px;
                font-size: 10px;
                margin: 2px 0px;
            }
        }

        @media (max-width: 320px) {
            .seat-btn {
                width: 26px;
                height: 24px;
                font-size: 8px;
            }
        }

        .seat-btn.selected {
            background-color: #7367f0 !important;
            color: #ffffff;
            border-color: #7367f0;
        }

        .seat-btn.booked {
            background-color: #2b2149 !important;
            color: #ffffff;
            border-color: #2b2149;
        }

        .seat-btn.booked-female {
            background-color: #b05eef;
            border-color: #b05eef;
            color: #ffffff;
        }

        .seat-btn.booked-others {
            background-color: #a22929;
            border-color: #a22929;
            color: #ffffff;
        }


        .driver-seat:disabled {
            background: #7367f0;
            color: #fff;
            pointer-events: none;
        }

        /* seat-plan-wrapper css end */
    </style>
@endpush
