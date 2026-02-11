@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('Trip ID')</th>
                                    <th>@lang('Trip Name')</th>
                                    <th>@lang('Route')</th>
                                    <th>@lang('Departure Date & Time')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center">@lang('Seat Price')</th>
                                    <th class="text-center">@lang('Occupancy')</th>
                                    <th class="text-center">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trips as $trip)
                                    <tr>
                                        <td>
                                            <span class="badge badge--light text--dark border px-3 py-2 fw-bold">
                                                TRP-{{ sprintf('%05d', $trip->id) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="user">
                                                <div class="desc">
                                                    <span class="fw-bold text--primary text--small">{{ __($trip->title) }}</span>
                                                    @if($trip->schedule_id)
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-sync-alt text--info"></i> 
                                                            <a href="{{ route('owner.trip.schedule.edit', $trip->schedule_id) }}" class="text--info">@lang('Schedule')</a>
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="fw-bold">{{ __($trip->startingPoint->name ?? 'N/A') }}</span>
                                                <i class="fas @if(isRTL()) fa-long-arrow-alt-left @else fa-long-arrow-alt-right @endif mx-2 text--primary"></i>
                                                <span class="fw-bold">{{ __($trip->destinationPoint->name ?? 'N/A') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text--small">
                                                <i class="far fa-calendar-alt text--muted me-1"></i>
                                                {{ showDateTime($trip->departure_datetime, 'Y-m-d') }}
                                                <br>
                                                <span class="fw-bold">{{ showDateTime($trip->departure_datetime, 'h:i A') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $tripStatus = 'Upcoming';
                                                $statusClass = 'badge--info';
                                                
                                                if($trip->status == Status::DISABLE) {
                                                    $tripStatus = 'Disabled';
                                                    $statusClass = 'badge--danger';
                                                } elseif (\Carbon\Carbon::parse($trip->departure_datetime)->isPast()) {
                                                    $tripStatus = 'Completed';
                                                    $statusClass = 'badge--success';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }} px-3 py-1">@lang($tripStatus)</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($trip->base_price) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $booked = $trip->bookedTickets->sum('seats_count') ?? 0;
                                                $capacity = $trip->fleetType->deck_seats ?? 30; // Fallback
                                                $percent = ($booked / $capacity) * 100;
                                            @endphp
                                            <div class="occupancy-info">
                                                <span class="fw-bold">{{ $booked }}/{{ $capacity }}</span>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg--primary" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.trip.show', $trip->id) }}" class="btn btn-sm btn-outline--info" title="@lang('View Details')">
                                                    <i class="la la-eye"></i>
                                                </a>
                                                <a href="{{ route('owner.trip.form', $trip->id) }}" class="btn btn-sm btn-outline--primary" title="@lang('Edit')">
                                                    <i class="la la-pencil"></i>
                                                </a>
                                                @if ($trip->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this trip?')"
                                                        data-action="{{ route('owner.trip.status', $trip->id) }}" title="@lang('Enable')">
                                                        <i class="la la-toggle-on"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this trip?')"
                                                        data-action="{{ route('owner.trip.status', $trip->id) }}" title="@lang('Disable')">
                                                        <i class="la la-toggle-off"></i>
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
                @if ($trips->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($trips) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.trip.form') }}" class="btn btn-sm btn-outline--primary me-2 mt-1">
        <i class="las la-plus"></i> @lang('Add New Trip')
    </a>
    <x-search-form placeholder="Search trips..." />
@endpush

@push('style')
<style>
    .occupancy-info {
        min-width: 80px;
    }
    .route-info {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .badge--upcoming { background-color: #e3f2fd; color: #1976d2; }
    .badge--completed { background-color: #e8f5e9; color: #2e7d32; }
</style>
@endpush
