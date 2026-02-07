@extends('supervisor.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg--primary">
                    <h4 class="card-title text-white mb-0">@lang('Ticket Details') #{{ $ticket->pnr ?? $ticket->id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <span class="d-block text-muted">@lang('Passenger Name')</span>
                            <h5 class="fw-bold">{{ $ticket->passenger_details->name ?? ($ticket->passenger->firstname . ' ' . $ticket->passenger->lastname) }}</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="d-block text-muted">@lang('Status')</span>
                            @if($ticket->is_boarded)
                                <span class="badge badge--success px-3 py-2">@lang('Boarded')</span>
                            @else
                                <span class="badge badge--warning px-3 py-2">@lang('Not Boarded')</span>
                            @endif
                        </div>
                    </div>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Trip Route')
                            <span class="fw-bold">{{ $ticket->trip->route->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Seat Number')
                            <span class="fw-bold">{{ implode(', ', $ticket->seats) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Journey Date')
                            <span class="fw-bold">{{ $ticket->date_of_journey }}</span>
                        </li>
                    </ul>

                    @if(!$ticket->is_boarded)
                        <form action="{{ route('supervisor.scanner.board', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn--success btn-lg w-100"><i class="las la-sign-in-alt"></i> @lang('Board Passenger')</button>
                        </form>
                    @else
                        <div class="alert alert-success text-center">
                            <i class="las la-check-circle"></i> @lang('Passenger Checked-in at') {{ $ticket->boarded_at }}
                        </div>
                    @endif
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('supervisor.scanner.index') }}" class="btn btn-link">@lang('Scan Next Ticket')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
