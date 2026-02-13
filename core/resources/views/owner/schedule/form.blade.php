@extends('owner.layouts.app')
@section('panel')
    <style>
        .section-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #ea5455;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-header h6 {
            margin-bottom: 0;
            color: #444;
            font-weight: 700;
        }
        .form-group label {
            font-weight: 600;
            color: #555;
        }
        .inherited-amenity-badge {
            background: #f1faff;
            border: 1px solid #cceeff;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: #007bff;
        }
        .inherited-amenity-badge i {
            font-size: 1.5rem;
        }
        .inherited-amenity-badge span {
            font-size: 0.75rem;
            font-weight: 600;
        }
        .amenity-item {
            cursor: pointer;
            width: 100%;
            margin-bottom: 0;
        }
        .amenity-box {
            background: #fff;
            border: 1px solid #e9ecef;
            padding: 15px 10px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .amenity-box i {
            font-size: 1.8rem;
            color: #6c757d;
        }
        .amenity-box span {
            font-size: 0.85rem;
            color: #495057;
            font-weight: 500;
        }
        input[type="checkbox"]:checked + .amenity-item .amenity-box {
            border-color: #28c76f;
            background-color: #f0fff5;
            box-shadow: 0 4px 12px rgba(40, 199, 111, 0.15);
        }
        input[type="checkbox"]:checked + .amenity-item .amenity-box i,
        input[type="checkbox"]:checked + .amenity-item .amenity-box span {
            color: #28c76f;
        }
        .policy-card {
            cursor: pointer;
            width: 100%;
        }
        .policy-card .card-content {
            border: 2px solid #e9ecef;
            padding: 15px;
            border-radius: 10px;
            transition: all 0.2s;
            height: 100%;
        }
        .policy-card .card-content h6 {
            margin-bottom: 5px;
            color: #333;
        }
        input[type="radio"]:checked + .policy-card .card-content {
            border-color: #ea5455;
            background-color: #fff9f9;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <form action="{{ route('owner.trip.schedule.store', @$schedule->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- General Info Section -->
                        <div class="section-header mt-2">
                            <i class="fas fa-info-circle text--primary"></i>
                            <h6>@lang('General Information')</h6>
                        </div>
                        <div class="row px-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-tag me-1"></i> @lang('Schedule Name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', @$schedule->name) }}" required placeholder="@lang('e.g., Morning Express A-B')"/>
                                    <small class="text-muted">@lang('Internal name for this schedule template')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-route me-1"></i> @lang('Route')</label>
                                    <select class="select2 form-control" name="route_id" required>
                                        <option selected value="">@lang('Select One')</option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}" @selected(old('route_id', @$schedule->route_id) == $route->id)>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt text--success me-1"></i> @lang('From')</label>
                                    <input type="hidden" name="starting_point" id="starting_point_id" value="{{ @$schedule->starting_point }}">
                                    <input type="text" class="form-control" id="starting_point_display" value="{{ @$schedule->startingPoint->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt text--danger me-1"></i> @lang('To')</label>
                                    <input type="hidden" name="destination_point" id="destination_point_id" value="{{ @$schedule->destination_point }}">
                                    <input type="text" class="form-control" id="destination_point_display" value="{{ @$schedule->destinationPoint->name }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-bus me-1"></i> @lang('Fleet Type')</label>
                                    <select class="select2 form-control" name="fleet_type_id" required>
                                        <option selected value="">@lang('Select One')</option>
                                        @foreach ($fleetTypes as $fleetType)
                                            <option value="{{ $fleetType->id }}" @selected(old('fleet_type_id', @$schedule->fleet_type_id) == $fleetType->id)>
                                                {{ $fleetType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-id-card me-1"></i> @lang('Default Vehicle (Optional)')</label>
                                    <select class="select2 form-control vehicle-select" name="vehicle_id">
                                        <option value="0">@lang('Assign per instance')</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" data-fleet-type="{{ $vehicle->fleet_type_id }}" @selected(old('vehicle_id', @$schedule->vehicle_id) == $vehicle->id)>
                                                {{ $vehicle->registration_no }} ({{ $vehicle->nick_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-sort-amount-up me-1"></i> @lang('Search Priority')</label>
                                    <input type="number" name="search_priority" class="form-control" value="{{ old('search_priority', @$schedule->search_priority ?? 50) }}" required min="0" max="100"/>
                                    <small class="text-muted">@lang('0-100, higher shows first')</small>
                                </div>
                            </div>
                        </div>

                        <!-- Timing & Recurrence Section -->
                        <div class="section-header mt-4">
                            <i class="fas fa-calendar-alt text--primary"></i>
                            <h6>@lang('Timing & Recurrence')</h6>
                        </div>
                        <div class="row px-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="far fa-clock me-1"></i> @lang('Departure Time')</label>
                                    <input type="time" name="starts_from" class="form-control" value="{{ old('starts_from', showDateTime(@$schedule->starts_from, 'H:i')) }}" required/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-clock me-1"></i> @lang('Arrival Time (Estimate)')</label>
                                    <input type="time" name="ends_at" class="form-control" value="{{ old('ends_at', showDateTime(@$schedule->ends_at, 'H:i')) }}" required/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-hourglass-half me-1"></i> @lang('Duration Hours')</label>
                                    <input type="number" name="duration_hours" class="form-control" value="{{ old('duration_hours', @$schedule->duration_hours ?? 0) }}" required min="0"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-hourglass-start me-1"></i> @lang('Duration Minutes')</label>
                                    <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', @$schedule->duration_minutes ?? 0) }}" required min="0" max="59"/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-redo me-1"></i> @lang('Recurrence Type')</label>
                                    <select class="form-control" name="recurrence_type" id="recurrence_type" required>
                                        <option value="daily" @selected(old('recurrence_type', @$schedule->recurrence_type) == 'daily')>@lang('Daily')</option>
                                        <option value="weekly" @selected(old('recurrence_type', @$schedule->recurrence_type) == 'weekly')>@lang('Weekly')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 recurrence-days-wrapper @if(old('recurrence_type', @$schedule->recurrence_type) != 'weekly') d-none @endif">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-check me-1"></i> @lang('Select Days')</label>
                                    <div class="d-flex flex-wrap gap-3 mt-2">
                                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $key => $day)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="recurrence_days[]" value="{{ $key }}" id="day_{{ $key }}"
                                                    @if(is_array(old('recurrence_days', @$schedule->recurrence_days)) && in_array($key, old('recurrence_days', @$schedule->recurrence_days))) checked @endif>
                                                <label class="form-check-label" for="day_{{ $key }}">{{ __($day) }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-play me-1"></i> @lang('Starts On')</label>
                                    <input type="date" name="starts_on" class="form-control" value="{{ old('starts_on', @$schedule->starts_on ? showDateTime(@$schedule->starts_on, 'Y-m-d') : date('Y-m-d')) }}" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-stop me-1"></i> @lang('Ends On')</label>
                                    <input type="date" name="ends_on" class="form-control" id="ends_on" value="{{ old('ends_on', @$schedule->ends_on ? showDateTime(@$schedule->ends_on, 'Y-m-d') : '') }}" @if(old('never_ends', @$schedule->never_ends)) disabled @endif/>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-group mb-0 mt-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="never_ends" id="never_ends" value="1" @checked(old('never_ends', @$schedule->never_ends ?? true))>
                                        <label class="form-check-label ms-2" for="never_ends">@lang('Never Ends')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Phase 1.2: Boarding & Dropping Points -->
                        <div class="section-header mt-4">
                            <i class="fas fa-map-marked-alt text--primary"></i>
                            <h6>@lang('Boarding & Dropping Points')</h6>
                        </div>
                        <div class="row px-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-sign-in-alt me-1 text--success"></i> @lang('Boarding Points')</label>
                                    <small class="d-block text-muted mb-2">@lang('Select pickup stops and their time offset from departure')</small>
                                    <div id="boarding-points-list" class="border rounded p-2" style="min-height: 100px; background: #f9f9f9;">
                                        @if(isset($schedule) && $schedule->scheduleBoardingPoints->count() > 0)
                                            @foreach($schedule->scheduleBoardingPoints as $sbp)
                                                <div class="d-flex align-items-center gap-2 mb-2 boarding-point-item p-2 bg-white border rounded">
                                                    <input type="hidden" name="boarding_points[{{ $loop->index }}][point_id]" value="{{ $sbp->boarding_point_id }}">
                                                    <input type="hidden" name="boarding_points[{{ $loop->index }}][offset_minutes]" value="{{ $sbp->time_offset_minutes }}">
                                                    <span class="flex-grow-1"><strong>{{ $sbp->boardingPoint->name }}</strong> (+{{ $sbp->time_offset_minutes }} min)</span>
                                                    <button type="button" class="btn btn-sm btn-outline--danger remove-point-btn"><i class="fas fa-times"></i></button>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center my-3"><small>@lang('No boarding points selected')</small></p>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline--primary mt-2" id="add-boarding-point-btn">
                                        <i class="fas fa-plus me-1"></i> @lang('Add Boarding Point')
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-sign-out-alt me-1 text--danger"></i> @lang('Dropping Points')</label>
                                    <small class="d-block text-muted mb-2">@lang('Select drop-off stops and their time offset from departure')</small>
                                    <div id="dropping-points-list" class="border rounded p-2" style="min-height: 100px; background: #f9f9f9;">
                                        @if(isset($schedule) && $schedule->scheduleDroppingPoints->count() > 0)
                                            @foreach($schedule->scheduleDroppingPoints as $sdp)
                                                <div class="d-flex align-items-center gap-2 mb-2 dropping-point-item p-2 bg-white border rounded">
                                                    <input type="hidden" name="dropping_points[{{ $loop->index }}][point_id]" value="{{ $sdp->dropping_point_id }}">
                                                    <input type="hidden" name="dropping_points[{{ $loop->index }}][offset_minutes]" value="{{ $sdp->time_offset_minutes }}">
                                                    <span class="flex-grow-1"><strong>{{ $sdp->droppingPoint->name }}</strong> (+{{ $sdp->time_offset_minutes }} min)</span>
                                                    <button type="button" class="btn btn-sm btn-outline--danger remove-point-btn"><i class="fas fa-times"></i></button>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center my-3"><small>@lang('No dropping points selected')</small></p>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline--primary mt-2" id="add-dropping-point-btn">
                                        <i class="fas fa-plus me-1"></i> @lang('Add Dropping Point')
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Policy Section -->
                        <div class="section-header mt-4">
                            <i class="fas fa-money-bill-wave text--primary"></i>
                            <h6>@lang('Pricing & Policy')</h6>
                        </div>
                        <div class="row px-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-coins me-1"></i> @lang('Default Base Price') ({{ gs('cur_sym') }})</label>
                                    <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price', @$schedule->base_price) }}" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-boxes me-1"></i> @lang('Inventory Allocation')</label>
                                    <select class="form-control" name="inventory_allocation" id="inventory_allocation" required>
                                        <option value="all_seats" @selected(old('inventory_allocation', @$schedule->inventory_allocation) == 'all_seats')>@lang('Full Bus Capacity')</option>
                                        <option value="limited" @selected(old('inventory_allocation', @$schedule->inventory_allocation) == 'limited')>@lang('Limited Allocation')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 inventory-count-wrapper @if(old('inventory_allocation', @$schedule->inventory_allocation) != 'limited') d-none @endif">
                                <div class="form-group">
                                    <label><i class="fas fa-chair me-1"></i> @lang('Allocated Seats Count')</label>
                                    <input type="number" name="inventory_count" class="form-control" value="{{ old('inventory_count', @$schedule->inventory_count) }}"/>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-undo-alt me-1"></i> @lang('Cancellation Policy')</label>
                                    <div class="row g-3">
                                        @forelse($policies as $policy)
                                            <div class="col-md-6">
                                                <input type="radio" name="cancellation_policy_id" value="{{ $policy->id }}" id="policy-{{ $policy->id }}" class="d-none"
                                                    @checked(old('cancellation_policy_id', @$schedule->cancellation_policy_id) == $policy->id)>
                                                <label class="policy-card" for="policy-{{ $policy->id }}">
                                                    <div class="card-content">
                                                        <h6>{{ $policy->name }}</h6>
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $policy->description }}</small>
                                                    </div>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="col-12"><div class="alert alert-warning py-2 small">@lang('No policies configured')</div></div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-toggle-on me-1"></i> @lang('Default Trip Status')</label>
                                    <select class="form-control" name="trip_status" required>
                                        <option value="draft" @selected(old('trip_status', @$schedule->trip_status) == 'draft')>@lang('Draft (Manual approval per trip)')</option>
                                        <option value="active" @selected(old('trip_status', @$schedule->trip_status) == 'active')>@lang('Active (Auto-published)')</option>
                                        <option value="pending" @selected(old('trip_status', @$schedule->trip_status) == 'pending')>@lang('Pending')</option>
                                        <option value="approved" @selected(old('trip_status', @$schedule->trip_status) == 'approved')>@lang('Approved')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Classification & Amenities Section -->
                        <div class="section-header mt-4">
                            <i class="fas fa-star text--primary"></i>
                            <h6>@lang('Trip Classification & Amenities')</h6>
                        </div>
                        <div class="row px-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-layer-group me-1"></i> @lang('Trip Type')</label>
                                    <select class="form-control" name="trip_type" required>
                                        <option value="local" @selected(old('trip_type', @$schedule->trip_type) == 'local')>@lang('Local')</option>
                                        <option value="express" @selected(old('trip_type', @$schedule->trip_type) == 'express')>@lang('Express')</option>
                                        <option value="semi_express" @selected(old('trip_type', @$schedule->trip_type) == 'semi_express')>@lang('Semi-Express')</option>
                                        <option value="night" @selected(old('trip_type', @$schedule->trip_type) == 'night')>@lang('Night Service')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-medal me-1"></i> @lang('Trip Category')</label>
                                    <select class="form-control" name="trip_category" required>
                                        <option value="standard" @selected(old('trip_category', @$schedule->trip_category) == 'standard')>@lang('Standard')</option>
                                        <option value="premium" @selected(old('trip_category', @$schedule->trip_category) == 'premium')>@lang('Premium')</option>
                                        <option value="budget" @selected(old('trip_category', @$schedule->trip_category) == 'budget')>@lang('Budget')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="fas fa-info me-1"></i> @lang('Bus Type Name')</label>
                                    <input type="text" name="bus_type" class="form-control" value="{{ old('bus_type', @$schedule->bus_type) }}" placeholder="@lang('e.g., Volvo Multi-Axle')"/>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                {{-- Vehicle Amenities (Inherited from Vehicle) --}}
                                <div class="form-group mb-4">
                                    <label class="fw-bold mb-2">
                                        <i class="las la-bus me-1 text--primary"></i>
                                        @lang('Inherited Vehicle Amenities') 
                                        <span class="badge badge--info ms-2">@lang('Read-only')</span>
                                    </label>
                                    
                                    <div id="inheritedAmenitiesDisplay" class="inherited-amenities-wrapper bg-light p-3 rounded" style="display:none;">
                                        <div class="row g-2" id="inheritedAmenitiesGrid">
                                            {{-- Populated via JS --}}
                                        </div>
                                    </div>
                                    
                                    <div id="noVehicleSelectedMessage" class="alert alert-light border py-2">
                                        <small class="text-muted"><i class="las la-info-circle me-1"></i> @lang('Select a vehicle in General Information to see its built-in features')</small>
                                    </div>
                                </div>

                                {{-- Trip Service Options --}}
                                <div class="form-group">
                                    <label class="fw-bold mb-2">
                                        <i class="las la-concierge-bell me-1 text--primary"></i>
                                        @lang('Trip Service Options')
                                        <span class="badge badge--success ms-2">@lang('Configurable')</span>
                                    </label>
                                    <div class="row g-3">
                                        @forelse($tripAmenities as $amenity)
                                            <div class="col-md-3 col-6">
                                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity-{{ $amenity->id }}" class="d-none"
                                                    @if(is_array(old('amenities', @$schedule->amenities)) && in_array($amenity->id, old('amenities', @$schedule->amenities))) checked @endif>
                                                <label class="amenity-item" for="amenity-{{ $amenity->id }}">
                                                    <div class="amenity-box">
                                                        <i class="{{ $amenity->icon }}"></i>
                                                        <span>{{ $amenity->label }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-muted small">@lang('No service options available')</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Surcharges Section -->
                        <div class="section-header mt-4">
                            <i class="fas fa-percentage text--primary"></i>
                            <h6>@lang('Pricing Surcharges (%)')</h6>
                        </div>
                        <div class="row px-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar-plus me-1 text--warning"></i> @lang('Weekend Surcharge')</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="weekend_surcharge" class="form-control" value="{{ old('weekend_surcharge', @$schedule->weekend_surcharge ?? 0) }}"/>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-umbrella-beach me-1 text--danger"></i> @lang('Holiday Surcharge')</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="holiday_surcharge" class="form-control" value="{{ old('holiday_surcharge', @$schedule->holiday_surcharge ?? 0) }}"/>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-bird me-1 text--success"></i> @lang('Early Bird Discount')</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="early_bird_discount" class="form-control" value="{{ old('early_bird_discount', @$schedule->early_bird_discount ?? 0) }}"/>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fas fa-bolt me-1 text--primary"></i> @lang('Last Minute Surcharge')</label>
                                    <div class="input-group">
                                        <input type="number" step="0.1" name="last_minute_surcharge" class="form-control" value="{{ old('last_minute_surcharge', @$schedule->last_minute_surcharge ?? 0) }}"/>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 py-4">
                        <button type="submit" class="btn btn--primary w-100 h-45 shadow">
                            <i class="fas fa-save me-1"></i> @lang('Save Schedule Template')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Phase 1.2: Boarding Point Selection Modal -->
    <div class="modal fade" id="boardingPointModal" tabindex="-1" aria-labelledby="boardingPointModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="boardingPointModalLabel">
                        <i class="fas fa-sign-in-alt text--success me-2"></i>Add Boarding Point
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="boarding-point-select"><i class="fas fa-map-marker-alt me-1"></i> Select Boarding Point</label>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-control flex-grow-1" id="boarding-point-select">
                                <option value="">-- Select Point --</option>
                                @foreach($boardingPoints as $bp)
                                    <option value="{{ $bp->id }}" data-name="{{ $bp->name }}" data-city="{{ $bp->city->name ?? '' }}">
                                        {{ $bp->name }} @if($bp->city), {{ $bp->city->name }}@endif
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('owner.counter.index') }}" target="_blank" class="btn btn-sm btn-outline--primary" title="Manage Boarding Points">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Don't see your point? 
                            <a href="{{ route('owner.counter.index') }}" target="_blank" class="text--primary">Add it in Branches & Offices</a>
                        </small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="boarding-offset-input"><i class="far fa-clock me-1"></i> Time Offset (minutes from departure)</label>
                        <input type="number" class="form-control" id="boarding-offset-input" min="0" step="1" placeholder="e.g., 0, 15, 30">
                        <small class="text-muted">0 = departure time, 15 = 15 minutes after departure</small>
                    </div>
                    <div class="form-group mb-0">
                        <label><i class="fas fa-clock me-1"></i> Calculated Time</label>
                        <div class="alert alert-info py-2" id="boarding-calculated-time">
                            <small>Select departure time in "Timing & Recurrence" section first</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn--primary" id="confirm-add-boarding-point">
                        <i class="fas fa-plus me-1"></i> Add Point
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 1.2: Dropping Point Selection Modal -->
    <div class="modal fade" id="droppingPointModal" tabindex="-1" aria-labelledby="droppingPointModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="droppingPointModalLabel">
                        <i class="fas fa-sign-out-alt text--danger me-2"></i>Add Dropping Point
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="dropping-point-select"><i class="fas fa-map-marker-alt me-1"></i> Select Dropping Point</label>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-control flex-grow-1" id="dropping-point-select">
                                <option value="">-- Select Point --</option>
                                @foreach($droppingPoints as $dp)
                                    <option value="{{ $dp->id }}" data-name="{{ $dp->name }}" data-city="{{ $dp->city->name ?? '' }}">
                                        {{ $dp->name }} @if($dp->city), {{ $dp->city->name }}@endif
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('owner.counter.index') }}" target="_blank" class="btn btn-sm btn-outline--primary" title="Manage Dropping Points">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Don't see your point? 
                            <a href="{{ route('owner.counter.index') }}" target="_blank" class="text--primary">Add it in Branches & Offices</a>
                        </small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="dropping-offset-input"><i class="far fa-clock me-1"></i> Time Offset (minutes from departure)</label>
                        <input type="number" class="form-control" id="dropping-offset-input" min="0" step="1" placeholder="e.g., 60, 90, 120">
                        <small class="text-muted">Duration from departure to this stop</small>
                    </div>
                    <div class="form-group mb-0">
                        <label><i class="fas fa-clock me-1"></i> Calculated Time</label>
                        <div class="alert alert-info py-2" id="dropping-calculated-time">
                            <small>Select departure time in "Timing & Recurrence" section first</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn--primary" id="confirm-add-dropping-point">
                        <i class="fas fa-plus me-1"></i> Add Point
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.trip.schedule.index') }}" class="btn btn-sm btn-outline--dark">
        <i class="fas fa-undo me-1"></i> @lang('Back to List')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.select2').select2();

            $('#recurrence_type').on('change', function() {
                if ($(this).val() == 'weekly') {
                    $('.recurrence-days-wrapper').removeClass('d-none');
                } else {
                    $('.recurrence-days-wrapper').addClass('d-none');
                }
            });

            $('#never_ends').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#ends_on').prop('disabled', true).val('');
                } else {
                    $('#ends_on').prop('disabled', false);
                }
            });

            $('#inventory_allocation').on('change', function() {
                if ($(this).val() == 'limited') {
                    $('.inventory-count-wrapper').removeClass('d-none');
                } else {
                    $('.inventory-count-wrapper').addClass('d-none');
                }
            });

            const routesData = @json($routes);

            $('select[name="route_id"]').on('change', function() {
                const routeId = $(this).val();
                const route = routesData.find(r => r.id == routeId);

                if (route) {
                    // Try to get names from loaded relationships, fallback to 'N/A'
                    const sourceName = route.starting_point ? route.starting_point.name : (route.startingPoint ? route.startingPoint.name : 'N/A');
                    const destinationName = route.destination_point ? route.destination_point.name : (route.destinationPoint ? route.destinationPoint.name : 'N/A');

                    $('#starting_point_id').val(route.starting_point || route.starting_point_id); // Use ID from relation or direct attribute
                    $('#starting_point_display').val(sourceName);

                    $('#destination_point_id').val(route.destination_point || route.destination_point_id);
                    $('#destination_point_display').val(destinationName);
                } else {
                    $('#starting_point_id').val('');
                    $('#starting_point_display').val('');
                    $('#destination_point_id').val('');
                    $('#destination_point_display').val('');
                }
            });

            // Vehicle Amenities Display Logic
            @php
                $vehiclesData = $vehicles->map(function($v) {
                    return [
                        'id' => $v->id,
                        'amenities' => $v->amenities->map(function($a) {
                            return [
                                'id' => $a->id,
                                'label' => $a->label,
                                'icon' => $a->icon
                            ];
                        })
                    ];
                });
            @endphp
            const vehiclesData = @json($vehiclesData);

            $('select[name="vehicle_id"]').on('change', function() {
                const vehicleId = $(this).val();
                if(!vehicleId || vehicleId == "0") {
                    $('#inheritedAmenitiesDisplay').hide();
                    $('#noVehicleSelectedMessage').show();
                    return;
                }

                const vehicle = vehiclesData.find(v => v.id == vehicleId);
                if(!vehicle || !vehicle.amenities || vehicle.amenities.length === 0) {
                    $('#inheritedAmenitiesDisplay').hide();
                    $('#noVehicleSelectedMessage').html(
                        '<small class="text-muted"><i class="las la-info-circle me-1"></i> @lang("This vehicle has no built-in amenities")</small>'
                    ).show();
                    return;
                }

                let html = '';
                vehicle.amenities.forEach(function(amenity) {
                    html += `
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="inherited-amenity-badge">
                                <i class="${amenity.icon}"></i>
                                <span>${amenity.label}</span>
                            </div>
                        </div>
                    `;
                });

                $('#inheritedAmenitiesGrid').html(html);
                $('#noVehicleSelectedMessage').hide();
                $('#inheritedAmenitiesDisplay').fadeIn();
            });

            @if(old('vehicle_id', @$schedule->vehicle_id))
                $('select[name="vehicle_id"]').trigger('change');
            @endif

            // Phase 1.2: Boarding/Dropping Points Management with Modals
            const boardingPointsData = @json($boardingPoints);
            const droppingPointsData = @json($droppingPoints);

            // Helper function to calculate time from offset
            function calculateTime(offsetMinutes) {
                const departureTime = $('input[name="starts_from"]').val();
                if (!departureTime) {
                    return null;
                }
                
                const [hours, minutes] = departureTime.split(':').map(Number);
                const totalMinutes = hours * 60 + minutes + parseInt(offsetMinutes);
                const newHours = Math.floor(totalMinutes / 60) % 24;
                const newMinutes = totalMinutes % 60;
                
                return String(newHours).padStart(2, '0') + ':' + String(newMinutes).padStart(2, '0');
            }

            // Update calculated time when offset changes
            $('#boarding-offset-input').on('input', function() {
                const offset = $(this).val();
                if (offset !== '' && offset >= 0) {
                    const calculatedTime = calculateTime(offset);
                    if (calculatedTime) {
                        $('#boarding-calculated-time').html(
                            '<strong class="text-success"><i class="fas fa-clock me-1"></i>' + calculatedTime + '</strong>'
                        );
                    } else {
                        $('#boarding-calculated-time').html(
                            '<small class="text-muted">Set departure time first</small>'
                        );
                    }
                }
            });

            $('#dropping-offset-input').on('input', function() {
                const offset = $(this).val();
                if (offset !== '' && offset >= 0) {
                    const calculatedTime = calculateTime(offset);
                    if (calculatedTime) {
                        $('#dropping-calculated-time').html(
                            '<strong class="text-success"><i class="fas fa-clock me-1"></i>' + calculatedTime + '</strong>'
                        );
                    } else {
                        $('#dropping-calculated-time').html(
                            '<small class="text-muted">Set departure time first</small>'
                        );
                    }
                }
            });

            // Open boarding point modal
            $('#add-boarding-point-btn').on('click', function() {
                $('#boarding-point-select').val('');
                $('#boarding-offset-input').val('');
                $('#boarding-calculated-time').html('<small class="text-muted">Enter time offset to see calculated time</small>');
                
                const boardingModal = new bootstrap.Modal(document.getElementById('boardingPointModal'));
                boardingModal.show();
            });

            // Open dropping point modal
            $('#add-dropping-point-btn').on('click', function() {
                $('#dropping-point-select').val('');
                $('#dropping-offset-input').val('');
                $('#dropping-calculated-time').html('<small class="text-muted">Enter time offset to see calculated time</small>');
                
                const droppingModal = new bootstrap.Modal(document.getElementById('droppingPointModal'));
                droppingModal.show();
            });

            // Confirm add boarding point
            $('#confirm-add-boarding-point').on('click', function() {
                const pointId = $('#boarding-point-select').val();
                const offsetMinutes = $('#boarding-offset-input').val();
                
                if (!pointId) {
                    alert('Please select a boarding point');
                    return;
                }
                
                if (offsetMinutes === '' || offsetMinutes < 0) {
                    alert('Please enter a valid time offset (0 or more minutes)');
                    return;
                }
                
                // Check for duplicates
                let isDuplicate = false;
                $('#boarding-points-list input[name$="[point_id]"]').each(function() {
                    if ($(this).val() == pointId) {
                        isDuplicate = true;
                        return false;
                    }
                });
                
                if (isDuplicate) {
                    alert('This boarding point is already added');
                    return;
                }
                
                const selectedOption = $('#boarding-point-select option:selected');
                const pointName = selectedOption.data('name');
                const cityName = selectedOption.data('city');
                const displayName = cityName ? pointName + ', ' + cityName : pointName;
                const calculatedTime = calculateTime(offsetMinutes);
                
                const index = $('#boarding-points-list .boarding-point-item').length;
                const html = `
                    <div class="d-flex align-items-center gap-2 mb-2 boarding-point-item p-2 bg-white border rounded">
                        <input type="hidden" name="boarding_points[${index}][point_id]" value="${pointId}">
                        <input type="hidden" name="boarding_points[${index}][offset_minutes]" value="${offsetMinutes}">
                        <div class="flex-grow-1">
                            <strong>${displayName}</strong>
                            <br><small class="text-muted">+${offsetMinutes} min${calculatedTime ? ' → ' + calculatedTime : ''}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline--danger remove-point-btn"><i class="fas fa-times"></i></button>
                    </div>
                `;
                
                $('#boarding-points-list p.text-muted').remove();
                $('#boarding-points-list').append(html);
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('boardingPointModal')).hide();
            });

            // Confirm add dropping point
            $('#confirm-add-dropping-point').on('click', function() {
                const pointId = $('#dropping-point-select').val();
                const offsetMinutes = $('#dropping-offset-input').val();
                
                if (!pointId) {
                    alert('Please select a dropping point');
                    return;
                }
                
                if (offsetMinutes === '' || offsetMinutes < 0) {
                    alert('Please enter a valid time offset (0 or more minutes)');
                    return;
                }
                
                // Check for duplicates
                let isDuplicate = false;
                $('#dropping-points-list input[name$="[point_id]"]').each(function() {
                    if ($(this).val() == pointId) {
                        isDuplicate = true;
                        return false;
                    }
                });
                
                if (isDuplicate) {
                    alert('This dropping point is already added');
                    return;
                }
                
                const selectedOption = $('#dropping-point-select option:selected');
                const pointName = selectedOption.data('name');
                const cityName = selectedOption.data('city');
                const displayName = cityName ? pointName + ', ' + cityName : pointName;
                const calculatedTime = calculateTime(offsetMinutes);
                
                const index = $('#dropping-points-list .dropping-point-item').length;
                const html = `
                    <div class="d-flex align-items-center gap-2 mb-2 dropping-point-item p-2 bg-white border rounded">
                        <input type="hidden" name="dropping_points[${index}][point_id]" value="${pointId}">
                        <input type="hidden" name="dropping_points[${index}][offset_minutes]" value="${offsetMinutes}">
                        <div class="flex-grow-1">
                            <strong>${displayName}</strong>
                            <br><small class="text-muted">+${offsetMinutes} min${calculatedTime ? ' → ' + calculatedTime : ''}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline--danger remove-point-btn"><i class="fas fa-times"></i></button>
                    </div>
                `;
                
                $('#dropping-points-list p.text-muted').remove();
                $('#dropping-points-list').append(html);
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('droppingPointModal')).hide();
            });

            // Remove point (boarding or dropping)
            $(document).on('click', '.remove-point-btn', function() {
                $(this).closest('.boarding-point-item, .dropping-point-item').remove();
                
                // Reindex
                $('#boarding-points-list .boarding-point-item').each(function(index) {
                    $(this).find('input[name^="boarding_points"]').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', name);
                    });
                });
                
                $('#dropping-points-list .dropping-point-item').each(function(index) {
                    $(this).find('input[name^="dropping_points"]').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', name);
                    });
                });

                // Show placeholder if empty
                if ($('#boarding-points-list .boarding-point-item').length === 0) {
                    $('#boarding-points-list').html('<p class="text-muted text-center my-3"><small>@lang("No boarding points selected")</small></p>');
                }
                if ($('#dropping-points-list .dropping-point-item').length === 0) {
                    $('#dropping-points-list').html('<p class="text-muted text-center my-3"><small>@lang("No dropping points selected")</small></p>');
                }
            });

        })(jQuery);
    </script>
@endpush
