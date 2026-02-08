@extends('admin.layouts.app')
@section('panel')
    {{-- Statistics Cards --}}
    <div class="row gy-4 mb-4">
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--primary has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-ticket-alt f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Bookings')</span>
                            <h2 class="text-white">{{ $totalBookings }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--success has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-money-bill-wave f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Spent')</span>
                            <h2 class="text-white">{{ showAmount($totalSpent) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--danger has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-times-circle f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Cancelled Bookings')</span>
                            <h2 class="text-white">{{ $cancelledBookings }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--info has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-clock f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Upcoming Trips')</span>
                            <h2 class="text-white">{{ $upcomingTrips }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        {{-- Passenger Info --}}
        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Passenger Information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">{{ $passenger->firstname }} {{ $passenger->lastname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span class="fw-bold">{{ $passenger->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span class="fw-bold">{{ $passenger->dial_code }}{{ $passenger->mobile }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($passenger->status == 1)
                                <span class="badge badge--success">@lang('Active')</span>
                            @else
                                <span class="badge badge--danger">@lang('Banned')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email Verified')
                            @if($passenger->ev)
                                <span class="badge badge--success">@lang('Yes')</span>
                            @else
                                <span class="badge badge--warning">@lang('No')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile Verified')
                            @if($passenger->sv)
                                <span class="badge badge--success">@lang('Yes')</span>
                            @else
                                <span class="badge badge--warning">@lang('No')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Joined')
                            <span>{{ showDateTime($passenger->created_at, 'M d, Y') }}</span>
                        </li>
                    </ul>
                    <div class="mt-3">
                        @if($passenger->status == 1)
                            <form action="{{ route('admin.passengers.ban', $passenger->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn--danger btn-sm w-100">
                                    <i class="las la-ban"></i> @lang('Ban Passenger')
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.passengers.unban', $passenger->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn--success btn-sm w-100">
                                    <i class="las la-check-circle"></i> @lang('Unban Passenger')
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking History --}}
        <div class="col-xl-8 col-md-6">
            <div class="card">
                <div class="card-body p-0">
                    <h5 class="card-title p-3 border-bottom">@lang('Booking History')</h5>
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('PNR')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Date of Journey')</th>
                                    <th>@lang('Seats')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($passenger->bookedTickets as $ticket)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $ticket->trx }}</span>
                                        </td>
                                        <td>
                                            {{ @$ticket->trip->title ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ @$ticket->trip->route->name ?? '' }}</small>
                                        </td>
                                        <td>{{ $ticket->date_of_journey }}</td>
                                        <td>
                                            @if(is_array($ticket->seats))
                                                {{ implode(', ', $ticket->seats) }}
                                            @else
                                                {{ $ticket->seats }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($ticket->price) }}</span>
                                        </td>
                                        <td>
                                            @if($ticket->status == 1)
                                                <span class="badge badge--success">@lang('Confirmed')</span>
                                            @elseif($ticket->status == 0)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($ticket->status == 3)
                                                <span class="badge badge--danger">@lang('Cancelled')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Unknown')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No bookings found')</td>
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

@push('breadcrumb-plugins')
    <a href="{{ route('admin.passengers.index') }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back to List')
    </a>
@endpush
