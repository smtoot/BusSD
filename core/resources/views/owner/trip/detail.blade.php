@extends('owner.layouts.app')

@section('panel')
    <div class="row gy-4">
        <!-- Top Info Header -->
        <div class="col-12">
            <div class="card b-radius--10 bg--white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="trip-id-box me-3">
                                <span class="badge badge--dark px-3 py-2 fs-6">TRP-{{ sprintf('%05d', $trip->id) }}</span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ __($trip->title) }}</h3>
                                <p class="text-muted mb-0">
                                    {{ __($trip->startingPoint->name ?? 'N/A') }} 
                                    <i class="fas @if(isRTL()) fa-long-arrow-alt-left @else fa-long-arrow-alt-right @endif mx-2 text--primary"></i> 
                                    {{ __($trip->destinationPoint->name ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
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
                            <span class="badge {{ $statusClass }} px-4 py-2 me-3 fs-6">@lang($tripStatus)</span>
                            <a href="{{ route('owner.trip.form', $trip->id) }}" class="btn btn-outline--primary btn-sm px-3 py-2">
                                <i class="la la-pencil"></i> @lang('Edit Trip')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column: Journey & Features -->
        <div class="col-xl-8 col-lg-7">
            <div class="row gy-4">
                <!-- Journey Timeline -->
                <div class="col-12">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg--white border-bottom p-3">
                            <h5 class="card-title fw-bold mb-0">@lang('Journey Overview')</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="journey-timeline">
                                <div class="timeline-item start">
                                    <div class="timeline-marker bg--success"></div>
                                    <div class="timeline-content">
                                        <h6 class="fw-bold text--success mb-1">@lang('Departure')</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="@if(isRTL()) ms-4 @else me-4 @endif">
                                                <span class="d-block fw-bold fs-4">{{ showDateTime($trip->departure_datetime, 'h:i A') }}</span>
                                                <small class="text-muted">{{ showDateTime($trip->departure_datetime, 'D, d M Y') }}</small>
                                            </div>
                                            <div class="@if(isRTL()) pe-3 border-end @else ps-3 border-start @endif">
                                                <span class="fw-bold d-block">{{ __($trip->startingPoint->name ?? 'N/A') }}</span>
                                                <small class="text-muted text-uppercase">@lang('Origin Station')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item duration my-2">
                                    <div class="timeline-line"></div>
                                    <div class="timeline-duration-badge">
                                        @php
                                            $start = \Carbon\Carbon::parse($trip->departure_datetime);
                                            $end = \Carbon\Carbon::parse($trip->arrival_datetime);
                                            $diff = $start->diff($end);
                                        @endphp
                                        <i class="far fa-clock me-1"></i> {{ $diff->h }}@lang('h') {{ $diff->i }}@lang('m')
                                    </div>
                                </div>

                                <div class="timeline-item end">
                                    <div class="timeline-marker bg--danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="fw-bold text--danger mb-1">@lang('Arrival')</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="@if(isRTL()) ms-4 @else me-4 @endif">
                                                <span class="d-block fw-bold fs-4">{{ showDateTime($trip->arrival_datetime, 'h:i A') }}</span>
                                                <small class="text-muted">{{ showDateTime($trip->arrival_datetime, 'D, d M Y') }}</small>
                                            </div>
                                            <div class="@if(isRTL()) pe-3 border-end @else ps-3 border-start @endif">
                                                <span class="fw-bold d-block">{{ __($trip->destinationPoint->name ?? 'N/A') }}</span>
                                                <small class="text-muted text-uppercase">@lang('Destination Station')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="col-12">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg--white border-bottom p-3">
                            <h5 class="card-title fw-bold mb-0">@lang('Trip Amenities')</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @php
                                    // Fetch amenity details if stored as IDs
                                    $amenityIds = is_array($trip->amenities) ? $trip->amenities : [];
                                    $amenityDetails = \App\Models\AmenityTemplate::whereIn('id', $amenityIds)->get();
                                @endphp
                                @forelse($amenityDetails as $item)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center p-3 b-radius--5 bg--light">
                                            <div class="amenity-icon text--primary fs-4 me-3">
                                                <i class="{{ $item->icon }}"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block">{{ __($item->label) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-3 text-muted">
                                        <i class="las la-info-circle me-1"></i> @lang('No amenities specified for this trip')
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancellation Policy -->
                <div class="col-12">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg--white border-bottom p-3">
                            <h5 class="card-title fw-bold mb-0">@lang('Cancellation Policy')</h5>
                        </div>
                        <div class="card-body p-4">
                            @if($trip->cancellationPolicy)
                                <div class="policy-highlight p-3 b-radius--10 mb-4" style="background-color: #f8f9ff; border: 1px solid #eef0ff;">
                                    <div class="d-flex align-items-center">
                                        <div class="policy-icon bg--primary text-white b-radius--50 p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-contract"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ __($trip->cancellationPolicy->name) }}</h6>
                                            <small class="text-muted">@lang('Active policy for this journey')</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered custom--table">
                                        <thead class="bg--light">
                                            <tr>
                                                <th>@lang('Time Range')</th>
                                                <th class="text-center">@lang('Deduction')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($trip->cancellationPolicy->rules ?? [] as $rule)
                                            <tr>
                                                <td>
                                                    @if(($rule['hours_before'] ?? 0) > 0)
                                                        @lang('More than') {{ $rule['hours_before'] }} @lang('hours before departure')
                                                    @else
                                                        @lang('Less than') 24 @lang('hours before departure')
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold text--danger">
                                                    {{ 100 - ($rule['refund_percentage'] ?? 0) }}% @lang('Deduction')
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 bg--light b-radius--10">
                                    <i class="las la-shield-alt text--muted fs-1 mb-2"></i>
                                    <p class="text-muted mb-0">@lang('No specific cancellation policy assigned.')</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Resources & Pricing -->
        <div class="col-xl-4 col-lg-5">
            <div class="row gy-4 sticky-top" style="top: 20px;">
                <!-- Occupancy Card -->
                <div class="col-12">
                    <div class="card b-radius--10 border-0 shadow-sm pricing-card overflow-hidden">
                        <div class="card-body p-4 text-center">
                            @php
                                $booked = $trip->bookedTickets->sum('seats_count') ?? 0;
                                $capacity = $trip->fleetType->deck_seats ?? 30;
                                $percent = ($booked / $capacity) * 100;
                            @endphp
                            <div class="occupancy-wheel mb-3">
                                <div class="fs-1 fw-bold text--primary mb-0">{{ $booked }}</div>
                                <div class="text-muted fw-bold">@lang('Out of') {{ $capacity }} @lang('Seats Booked')</div>
                            </div>
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg--primary" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">{{ number_format($percent, 1) }}% @lang('Occupancy Rate')</small>
                        </div>
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="col-12">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg--white border-bottom p-3">
                            <h5 class="card-title fw-bold mb-0">@lang('Pricing Details')</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">@lang('Base Ticket Price')</span>
                                <span class="fw-bold fs-5 text--dark">{{ gs('cur_sym') }}{{ showAmount($trip->base_price) }}</span>
                            </div>
                            <hr class="my-3">
                            <div class="pricing-rules">
                                <div class="rule-item d-flex justify-content-between mb-2">
                                    <small class="text-muted">@lang('Weekend Surcharge')</small>
                                    <small class="fw-bold text--danger">+{{ $trip->weekend_surcharge }}%</small>
                                </div>
                                <div class="rule-item d-flex justify-content-between mb-2">
                                    <small class="text-muted">@lang('Holiday Surcharge')</small>
                                    <small class="fw-bold text--danger">+{{ $trip->holiday_surcharge }}%</small>
                                </div>
                                <div class="rule-item d-flex justify-content-between">
                                    <small class="text-muted">@lang('Early Bird Discount')</small>
                                    <small class="fw-bold text--success">-{{ $trip->early_bird_discount }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resource Assignment -->
                <div class="col-12">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg--white border-bottom p-3">
                            <h5 class="card-title fw-bold mb-0">@lang('Assigned Resources')</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="resource-item d-flex align-items-center mb-4 pb-3 border-bottom">
                                <div class="resource-icon bg--light b-radius--10 p-2 me-3">
                                    <i class="las la-bus text--primary fs-2"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block uppercase-text">@lang('Assigned vehicle')</small>
                                    <span class="fw-bold d-block">{{ $trip->vehicle->nick_name ?? trans('Not Assigned') }}</span>
                                    @if($trip->vehicle)
                                        <small class="badge badge--light border text--dark mt-1 px-2 py-0">{{ $trip->vehicle->register_no }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="resource-item d-flex align-items-center mb-4 pb-3 border-bottom">
                                <div class="resource-icon bg--light b-radius--10 p-2 me-3">
                                    <i class="las la-id-badge text--primary fs-2"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block uppercase-text">@lang('Primary Driver')</small>
                                    <span class="fw-bold d-block">{{ $trip->driver->name ?? trans('Not Assigned') }}</span>
                                </div>
                            </div>

                            <div class="resource-item d-flex align-items-center">
                                <div class="resource-icon bg--light b-radius--10 p-2 me-3">
                                    <i class="las la-tags text--primary fs-2"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block uppercase-text">@lang('Fleet Type')</small>
                                    <span class="fw-bold d-block">{{ __($trip->fleetType->name ?? 'N/A') }}</span>
                                    <small class="text-muted">{{ $trip->fleetType->has_ac ? trans('Air Conditioned') : trans('Non-AC') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .journey-timeline {
        position: relative;
        @if(isRTL()) padding-right: 20px; @else padding-left: 20px; @endif
    }
    .timeline-item {
        position: relative;
        @if(isRTL()) padding-right: 30px; @else padding-left: 30px; @endif
    }
    .timeline-marker {
        position: absolute;
        @if(isRTL()) right: 0; @else left: 0; @endif
        top: 5px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        z-index: 2;
    }
    .timeline-line {
        position: absolute;
        @if(isRTL()) right: 6px; @else left: 6px; @endif
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #eef0f7;
        z-index: 1;
    }
    .timeline-item.duration {
        height: 60px;
        display: flex;
        align-items: center;
        @if(isRTL()) padding-right: 0; @else padding-left: 0; @endif
    }
    .timeline-duration-badge {
        position: relative;
        z-index: 2;
        background: #f8f9ff;
        border: 1px solid #eef0f7;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        color: #6c757d;
        @if(isRTL()) margin-right: -5px; @else margin-left: -5px; @endif
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .timeline-item.end .timeline-marker {
        top: 5px;
    }
    .uppercase-text {
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 10px;
    }
    @if(isRTL())
    .resource-icon {
        margin-left: 1rem !important;
        margin-right: 0 !important;
    }
    .policy-icon {
        margin-left: 1rem !important;
        margin-right: 0 !important;
    }
    .amenity-icon {
        margin-left: 1rem !important;
        margin-right: 0 !important;
    }
    @endif
</style>
@endpush
