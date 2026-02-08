@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Booking Information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('PNR Number')
                            <span class="fw-bold">{{ $booking->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Operator')
                            <span class="fw-bold">{{ @$booking->owner->fullname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Channel')
                            @if($booking->passenger_id)
                                <span class="badge badge--primary">@lang('B2C')</span>
                            @else
                                <span class="badge badge--info">@lang('Counter')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($booking->status == 1)
                                <span class="badge badge--success">@lang('Confirmed')</span>
                            @elseif($booking->status == 3)
                                <span class="badge badge--danger">@lang('Cancelled')</span>
                            @else
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Booking Date')
                            <span>{{ showDateTime($booking->created_at) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Trip & Seat Details')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Trip')
                            <span class="fw-bold">{{ @$booking->trip->title }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Route')
                            <span class="fw-bold">{{ @$booking->trip->route->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Fleet Type')
                            <span class="fw-bold">{{ @$booking->trip->fleetType->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Seats Booked')
                            <span class="fw-bold">
                                @if(is_array($booking->seats))
                                    {{ implode(', ', $booking->seats) }}
                                @else
                                    {{ $booking->seats }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Total Amount')
                            <span class="fw-bold">{{ showAmount($booking->price) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Passenger Details')</h5>
                    <ul class="list-group">
                        @if($booking->passenger_id)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Name')
                                <span class="fw-bold">{{ @$booking->passenger->fullname }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email')
                                <span class="fw-bold">{{ @$booking->passenger->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Mobile')
                                <span class="fw-bold">{{ @$booking->passenger->mobile }}</span>
                            </li>
                        @else
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Counter')
                                <span class="fw-bold">{{ @$booking->counterManager->counter->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Counter Manager')
                                <span class="fw-bold">{{ @$booking->counterManager->fullname }}</span>
                            </li>
                             <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Mobile')
                                <span class="fw-bold">{{ @$booking->counterManager->mobile }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ url()->previous() }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back')
    </a>
@endpush
