@extends('owner.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-12 mb-30">
            <div class="booking-header d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div class="header-left">
                    <h5 class="mb-1 text-muted"><i class="las la-calendar-day"></i> {{ Carbon\Carbon::parse($date)->format('l, F d, Y') }}</h5>
                    <h2 class="mb-0 fw-bold">@lang("Today's Departures")</h2>
                    <p class="text-muted mb-0">{{ $trips->count() }} @lang('departures remaining today')</p>
                </div>
            </div>

            <div class="card b-radius--10 mb-4 search-card shadow-sm border-0">
                <div class="card-body">
                    <form action="" method="GET" class="row align-items-end g-3">
                        <div class="col-md-10">
                            <label class="form-label fw-bold"><i class="las la-search"></i> @lang('Find a Trip')</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="@lang('Search by date or trip number')..." value="{{ request()->search }}">
                                <input type="date" name="date" class="form-control border-start-0 ps-0" value="{{ $date }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn--primary w-100 py-2">@lang('Filter')</button>
                        </div>
                    </form>
                </div>
            </div>

            <h5 class="mb-3 fw-bold"><i class="las la-arrow-right text-success"></i> @lang('Upcoming Departures')</h5>

            <div class="row g-4">
                @forelse($trips as $trip)
                    <div class="col-xl-6 col-md-6 mb-30">
                        <div class="card b-radius--10 trip-card shadow-sm border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ __($trip->title) }}</h4>
                                        <p class="text-muted mb-0 small"><i class="las la-map-marker text--primary"></i> {{ __($trip->startingPoint->name) }} &rarr; {{ __($trip->destinationPoint->name) }}</p>
                                    </div>
                                    @php
                                        $departure = Carbon\Carbon::parse($trip->departure_datetime);
                                        $now = Carbon\Carbon::now();
                                        $diff = $now->diffInMinutes($departure, false);
                                        
                                        $status = 'Scheduled';
                                        $statusClass = 'badge--info';
                                        
                                        if ($diff <= 60 && $diff > 0) {
                                            $status = 'Boarding Now';
                                            $statusClass = 'badge--success';
                                        } elseif ($diff <= 0 && $diff > -60) {
                                            $status = 'Departing';
                                            $statusClass = 'badge--warning';
                                        } elseif ($diff <= -60) {
                                            $status = 'Departed';
                                            $statusClass = 'badge--secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-1">@lang($status)</span>
                                </div>

                                <div class="mb-4">
                                    <h5 class="fw-bold text--primary"><i class="las la-clock"></i> {{ Carbon\Carbon::parse($trip->departure_datetime)->format('H:i') }}</h5>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small">@lang('Check-in Progress')</span>
                                        <span class="fw-bold small" dir="ltr">{{ $trip->boardedCount() }} / {{ $trip->bookedCount() }} <span dir="rtl">@lang('checked-in')</span></span>
                                    </div>
                                    @php $progress = $trip->checkinProgress(); @endphp
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg--success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="text-muted small">
                                        <i class="las la-users"></i> <span dir="ltr">{{ $trip->bookedCount() }}</span> @lang('passengers') / <span dir="ltr">{{ $trip->fleetCapacity() }}</span> @lang('capacity')
                                    </div>
                                </div>

                                <a href="{{ route('owner.bookings.manage', $trip->id) }}" class="btn btn--success w-100 py-2 fw-bold">@lang('Manage Trip')</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="card b-radius--10 shadow-sm border-0 py-5">
                            <i class="las la-bus-alt la-5x text-muted mb-3"></i>
                            <h4 class="text-muted">@lang('No departures found for this date')</h4>
                            <a href="{{ route('owner.trip.form') }}" class="btn btn--primary mt-3">@lang('Add Trip Instance')</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .trip-card {
        transition: transform 0.2s;
    }
    .trip-card:hover {
        transform: translateY(-5px);
    }
    .btn--success {
        background-color: #00A34D !important;
        border-color: #00A34D !important;
    }
    .btn--success:hover {
        background-color: #008f43 !important;
    }
    .badge--success {
        background-color: #E6F6ED;
        color: #00A34D;
    }
    .badge--info {
        background-color: #EBF3FF;
        color: #1F78D1;
    }
    .progress-bar {
        border-radius: 4px;
    }
</style>
@endpush
