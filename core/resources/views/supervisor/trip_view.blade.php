@extends('supervisor.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="seat-plan-tab" data-bs-toggle="tab" data-bs-target="#seat-plan" type="button" role="tab" aria-controls="seat-plan" aria-selected="true">@lang('Seat Plan')</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="manifest-tab" data-bs-toggle="tab" data-bs-target="#manifest" type="button" role="tab" aria-controls="manifest" aria-selected="false">@lang('Passenger Manifest')</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        {{-- Tab 1: Seat Plan --}}
                        <div class="tab-pane fade show active" id="seat-plan" role="tabpanel" aria-labelledby="seat-plan-tab">
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
                                        <div class="diver-seat text-right">
                                            <button type="button" class="seat-btn driver-seat" disabled><i
                                                    class="la la-radiation-alt"></i></button>
                                        </div>
                                    @else
                                        <div class="diver-seat text-right">
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
                                            $lastRowSeat = $seat - $totalRow * $rowItem;
                                            $chr = 'A';
                                        @endphp
                                        @for ($i = 1; $i <= $totalRow; $i++)
                                            @php
                                                if ($lastRowSeat == 1 && $i == $totalRow - 1) {
                                                    break;
                                                }
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
                                            @php $seatNumber++ @endphp
                                            <div class="single-row d-flex justify-content-between">
                                                @for ($lsr = 1; $lsr <= $rowItem + 1; $lsr++)
                                                    <button type="button"
                                                        value="{{ $deck }} - {{ $seatNumber }}{{ $lsr }}"
                                                        class="seat-btn">{{ $seatNumber }}{{ $lsr }}</i></button>
                                                @endfor
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

                        {{-- Tab 2: Passenger Manifest --}}
                        <div class="tab-pane fade" id="manifest" role="tabpanel" aria-labelledby="manifest-tab">
                            <div class="table-responsive--sm table-responsive">
                                <table class="table table--light style--two">
                                    <thead>
                                        <tr>
                                            <th>@lang('Seat')</th>
                                            <th>@lang('Passenger')</th>
                                            <th>@lang('Route')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trip->bookedTickets as $ticket)
                                            @php
                                                $isAppBooking = $ticket->passenger_id ? true : false;
                                                $seats = implode(', ', $ticket->seats);
                                                $details = $ticket->passenger_details;
                                            @endphp
                                            <tr>
                                                <td class="fw-bold">{{ $seats }}</td>
                                                <td>
                                                    <div class="user">
                                                        <div class="thumb">
                                                            @if($isAppBooking)
                                                                <span class="avatar-initials bg--info" title="App Booking"><i class="las la-mobile-alt"></i></span>
                                                            @else
                                                                <span class="avatar-initials bg--secondary" title="Counter Booking"><i class="las la-store"></i></span>
                                                            @endif
                                                        </div>
                                                        <div class="info pl-2">
                                                            <h6 class="name">{{ $details->name ?? ($ticket->passenger->firstname . ' '. $ticket->passenger->lastname) }}</h6>
                                                            @if($isAppBooking)
                                                                <span class="badge badge--info">@lang('App User')</span>
                                                            @endif
                                                            @if($ticket->is_boarded)
                                                                <span class="badge badge--success">@lang('Boarded')</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="d-block">{{ $details->from ?? $ticket->pick_up_point }}</span>
                                                    <i class="las la-arrow-down"></i>
                                                    <span class="d-block">{{ $details->to ?? $ticket->dropping_point }}</span>
                                                </td>
                                                <td>
                                                    <a href="tel:{{ $details->mobile_number ?? $ticket->passenger->mobile }}" class="btn btn-sm btn--success">
                                                        <i class="las la-phone"></i> @lang('Call')
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center text-muted">@lang('No passengers booked yet')</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('supervisor.dashboard') }}" />
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {
            var booked_seats = JSON.parse('@php echo json_encode($trip->bookedTickets) @endphp');

            $.each(booked_seats, function(i, v) {
                $.each(v.seats, function(index, val) {
                    var title = `${v.passenger_details.from} To ${v.passenger_details.to}`;
                    if (v.passenger_details.gender == 1)
                        $(`.seat-btn[value="${val}"]`).addClass('booked').attr('disabled', 'disabled')
                        .attr('title', `${title}`).attr('data-toggle', 'tooltip');
                    else if (v.passenger_details.gender == 2)
                        $(`.seat-btn[value="${val}"]`).addClass('booked-female').attr('disabled',
                            'disabled').attr('title', `${title}`).attr('data-toggle', 'tooltip');
                    else if (v.passenger_details.gender == 0)
                        $(`.seat-btn[value="${val}"]`).addClass('booked-others').attr('disabled',
                            'disabled').attr('title', `${title}`).attr('data-toggle', 'tooltip');
                });
            });

            // Initialize Bootstrap tooltips
            $(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

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
