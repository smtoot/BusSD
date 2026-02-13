@extends('owner.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <!-- Wizard Progress Bar -->
                <div class="wizard-progress-wrapper p-4 border-bottom">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="wizard-progress">
                                <div class="step active" data-step="1">
                                    <div class="step-icon"><i class="fas fa-bus"></i></div>
                                    <div class="step-label">@lang('Trip Details')</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step" data-step="2">
                                    <div class="step-icon"><i class="fas fa-dollar-sign"></i></div>
                                    <div class="step-label">@lang('Pricing & Inventory')</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step" data-step="3">
                                    <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="step-label">@lang('Policies & Review')</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('owner.trip.store', @$trip->id) }}" method="POST" id="tripWizardForm">
                    @csrf
                    
                    <!-- Step 1: Trip Details -->
                    <div class="wizard-step p-4" id="step-1">
                        <h5 class="mb-4 text--primary"><i class="fas fa-info-circle me-2"></i>@lang('Basic Information & Schedule')</h5>
                        
                        <div class="row g-4">
                            <!-- Basic Info -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Trip Title')</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', @$trip->title) }}" required placeholder="@lang('e.g. Khartoum Express Morning')" />
                                    <small class="text-muted">@lang('Descriptive name for this specific trip')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Fleet Type') <span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="fleet_type" required>
                                        <option value="">@lang('Select Fleet Type')</option>
                                        @foreach ($fleetTypes as $fleetType)
                                            <option value="{{ $fleetType->id }}" data-name="{{ $fleetType->name }}" @selected(old('fleet_type', @$trip->fleet_type_id) == $fleetType->id)>
                                                {{ $fleetType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Route') <span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="route" required>
                                        <option value="">@lang('Select Route')</option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}" 
                                                    data-name="{{ $route->name }}"
                                                    data-source="{{ $route->startingPoint?->name }}"
                                                    data-source-id="{{ $route->starting_city_id }}"
                                                    data-destination="{{ $route->destinationPoint?->name }}"
                                                    data-destination-id="{{ $route->destination_city_id }}"
                                                    @selected(old('route', @$trip->route_id) == $route->id)>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Route Template (Optional)')</label>
                                    <select class="select2 form-control" name="route_template_id" id="route_template_id">
                                        <option value="">@lang('Select Template to Auto-fill Stops')</option>
                                        @foreach ($routeTemplates as $template)
                                            <option value="{{ $template->id }}" @selected(old('route_template_id', @$trip->route_template_id) == $template->id)>
                                                {{ $template->name }} ({{ $template->stops_count ?: $template->stops->count() }} @lang('Stops'))
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">@lang('Loading a template will automatically populate the trip timeline')</small>
                                </div>
                            </div>

                            <!-- Route Visual & Timeline -->
                            <div class="col-12 from-to-wrapper" style="display:none;"></div>
                            
                            <div class="col-12 route-stops-wrapper mt-3" style="display:none;">
                                <h6 class="text--primary mb-3"><i class="fas fa-map-marked-alt me-2"></i>@lang('Stops & Timing')</h6>
                                <div id="stopsTimeline" class="stops-timeline">
                                    {{-- Populated via AJAX --}}
                                </div>
                            </div>

                            <!-- Schedule (Datetime) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Departure Date & Time') <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="departure_datetime" class="form-control" 
                                           value="{{ old('departure_datetime', @$trip->departure_datetime ? \Carbon\Carbon::parse($trip->departure_datetime)->format('Y-m-d\TH:i') : '') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Arrival Date & Time') <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="arrival_datetime" class="form-control"
                                           value="{{ old('arrival_datetime', @$trip->arrival_datetime ? \Carbon\Carbon::parse($trip->arrival_datetime)->format('Y-m-d\TH:i') : '') }}" required>
                                    <small id="duration-display" class="text--info fw-bold mt-1 d-block"></small>
                                </div>
                            </div>

                            <!-- Staff Assignment -->
                            <div class="col-12 mt-4">
                                <h6 class="text--primary border-bottom pb-2 mb-3"><i class="fas fa-users-cog me-2"></i>@lang('Resource Assignment')</h6>
                            </div>

                            @if($branches->count() > 1)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Owning Branch')</label>
                                    <select class="select2 form-control" name="owning_branch_id">
                                        <option value="">@lang('Auto-assign (Primary Branch)')</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @selected(old('owning_branch_id', @$trip->owning_branch_id) == $branch->id)>
                                                {{ $branch->name }} @if($branch->code)({{ $branch->code }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">@lang('Select which branch owns and manages this trip')</small>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Vehicle') <span class="text-danger">*</span></label>
                                    <select class="select2 form-control vehicle-select" name="vehicle_id" required>
                                        <option value="">@lang('Select Vehicle')</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" data-fleet-type="{{ $vehicle->fleet_type_id }}"
                                                @selected(old('vehicle_id', $trip?->assignedBuses?->first()?->vehicle_id) == $vehicle->id)>
                                                {{ $vehicle->registration_no }} - {{ $vehicle->nick_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Driver')</label>
                                    <select class="select2 form-control" name="driver_id">
                                        <option value="">@lang('No Driver Assigned')</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}" @selected(old('driver_id', $trip?->assignedBuses?->first()?->driver_id) == $driver->id)>
                                                {{ $driver->fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Supervisor')</label>
                                    <select class="select2 form-control" name="supervisor_id">
                                        <option value="">@lang('No Supervisor Assigned')</option>
                                        @foreach ($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}" @selected(old('supervisor_id', $trip?->assignedBuses?->first()?->supervisor_id) == $supervisor->id)>
                                                {{ $supervisor->fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pricing & Inventory -->
                    <div class="wizard-step p-4" id="step-2" style="display:none;">
                        <h5 class="mb-4 text--primary"><i class="fas fa-coins me-2"></i>@lang('Pricing & Inventory Configuration')</h5>

                        <!-- Inventory Strategy -->
                        <div class="row mb-5">
                            <label class="fw-bold mb-3">@lang('Inventory Allocation Strategy')</label>
                            <div class="col-md-3">
                                <label class="inventory-card">
                                    <input type="radio" name="inventory_allocation" value="full" class="d-none" @checked(old('inventory_allocation', @$trip->inventory_allocation ?? 'full') == 'full')>
                                    <div class="card-content">
                                        <i class="fas fa-bus fa-2x mb-2 text-muted"></i>
                                        <h6>@lang('Full Bus')</h6>
                                        <p>@lang('Sell all seats')</p>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label class="inventory-card">
                                    <input type="radio" name="inventory_allocation" value="partial" class="d-none" @checked(old('inventory_allocation', @$trip->inventory_allocation) == 'partial')>
                                    <div class="card-content">
                                        <i class="fas fa-battery-half fa-2x mb-2 text-muted"></i>
                                        <h6>@lang('Partial')</h6>
                                        <p>@lang('Sell limited count')</p>
                                    </div>
                                </label>
                            </div>
                            <!-- Add input for count if partial is selected -->
                            <div class="col-md-12 mt-3" id="inventory-count-wrapper" style="display:none;">
                                <div class="form-group w-50">
                                    <label>@lang('Number of Seats to Sell')</label>
                                    <input type="number" name="inventory_count" class="form-control" value="{{ old('inventory_count', @$trip->inventory_count) }}">
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2">@lang('Base Pricing')</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Base Ticket Price')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input type="number" step="0.01" name="base_price" id="base_price" class="form-control" value="{{ old('base_price', @$trip->seat_price) }}" required>
                                        <button type="button" class="btn btn--info suggestPriceBtn" data-toggle="tooltip" title="@lang('Get Smart Pricing Suggestion')">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">@lang('Main route price (Source to Destination)')</small>
                                        <a href="javascript:void(0)" class="text--primary small fw-bold pricingBreakdownBtn">@lang('View Breakdown')</a>
                                    </div>
                                </div>
                            </div>

                            @if($seatModifiersCount > 0)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fw-bold">@lang('Seat-Level Pricing')</label>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-outline--primary seatPricingPreviewBtn">
                                            <i class="fas fa-chair me-2"></i> @lang('Preview Seat Premiums')
                                            <span class="badge badge--dark ms-2">{{ $seatModifiersCount }} @lang('Rules Active')</span>
                                        </button>
                                    </div>
                                    <small class="text-muted">@lang('Multiple pricing tiers detected for this fleet type')</small>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Dynamic Surcharges -->
                            <div class="col-12 mt-4">
                                <h6 class="border-bottom pb-2">@lang('Dynamic Pricing Rules')</h6>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Weekend Surcharge')</label>
                                    <div class="input-group">
                                        <input type="number" name="weekend_surcharge" class="form-control" value="{{ old('weekend_surcharge', @$trip->weekend_surcharge) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Holiday Surcharge')</label>
                                    <div class="input-group">
                                        <input type="number" name="holiday_surcharge" class="form-control" value="{{ old('holiday_surcharge', @$trip->holiday_surcharge) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Early Bird Discount')</label>
                                    <div class="input-group">
                                        <input type="number" name="early_bird_discount" class="form-control" value="{{ old('early_bird_discount', @$trip->early_bird_discount) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Policies & Features -->
                    <div class="wizard-step p-4" id="step-3" style="display:none;">
                        <h5 class="mb-4 text--primary"><i class="fas fa-clipboard-check me-2"></i>@lang('Trip Features & Policies')</h5>

                        {{-- Vehicle Amenities (Inherited from Vehicle) --}}
                        <div class="form-group mb-5">
                            <label class="fw-bold h6 mb-2">
                                <i class="las la-bus me-1"></i>
                                @lang('Vehicle Amenities') 
                                <span class="badge badge--info ms-2">@lang('Built-in Features')</span>
                            </label>
                            <p class="text-muted small mb-3">
                                @lang('These amenities are permanently installed in the vehicle and will be inherited automatically')
                            </p>
                            
                            <div id="inheritedAmenitiesDisplay" class="inherited-amenities-wrapper" style="display:none;">
                                <div class="row g-2" id="inheritedAmenitiesGrid">
                                    {{-- Will be populated via JavaScript when vehicle is selected --}}
                                </div>
                            </div>
                            
                            <div id="noVehicleSelectedMessage" class="alert alert-warning">
                                <i class="las la-info-circle me-2"></i>
                                @lang('Please select a vehicle in Step 1 to see its built-in amenities')
                            </div>
                        </div>

                        {{-- Trip Options (Service Offerings) --}}
                        <div class="form-group mb-5">
                            <label class="fw-bold h6 mb-2">
                                <i class="las la-concierge-bell me-1"></i>
                                @lang('Trip Service Options')
                                <span class="badge badge--success ms-2">@lang('Configurable')</span>
                            </label>
                            <p class="text-muted small mb-3">
                                @lang('Select optional services you will provide during this trip')
                            </p>
                            <div class="amenities-grid-wrapper">
                                <div class="row g-3">
                                    @forelse($tripAmenities as $amenity)
                                        <div class="col-6 col-md-3 col-lg-2">
                                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity-{{ $amenity->id }}" class="d-none"
                                                @if(@$trip && is_array($trip->amenities) && in_array($amenity->id, $trip->amenities)) checked @endif>
                                            <label class="amenity-item" for="amenity-{{ $amenity->id }}">
                                                <div class="amenity-box">
                                                    <i class="{{ $amenity->icon }}"></i>
                                                    <span>{{ $amenity->label }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted">@lang('No trip service options available')</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Policy -->
                        <div class="form-group mb-5">
                            <label class="fw-bold h6 mb-3">@lang('Cancellation Policy')</label>
                            <div class="row g-3">
                                @forelse($policies as $policy)
                                    <div class="col-md-4">
                                        <input type="radio" name="cancellation_policy_id" value="{{ $policy->id }}" id="policy-{{ $policy->id }}" class="d-none"
                                            @checked(old('cancellation_policy_id', @$trip->cancellation_policy_id) == $policy->id)>
                                        <label class="policy-card" for="policy-{{ $policy->id }}">
                                            <div class="card-content">
                                                <h6>{{ $policy->name }}</h6>
                                                <small>{{ $policy->description }}</small>
                                            </div>
                                        </label>
                                    </div>
                                @empty
                                    <div class="col-12"><div class="alert alert-warning">@lang('No policies configured')</div></div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Status & Publish -->
                        <div class="form-group bg-light p-3 rounded">
                            <label class="fw-bold">@lang('Trip Status')</label>
                            <div class="d-flex gap-4 mt-2">
                                <label class="radio-inline">
                                    <input type="radio" name="trip_status" value="active" @checked(!@$trip || @$trip->trip_status == 'active')> 
                                    <span class="badge badge--success">@lang('Active (Published)')</span>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="trip_status" value="draft" @checked(@$trip->trip_status == 'draft')>
                                    <span class="badge badge--dark">@lang('Draft')</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Navigation -->
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-lg px-5" id="prevBtn" style="display:none;">
                                <i class="fas fa-arrow-left me-2"></i>@lang('Back')
                            </button>
                            
                            <div class="ms-auto">
                                <button type="button" class="btn btn--primary btn-lg px-5" id="nextBtn">
                                    @lang('Next Step')<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" class="btn btn--success btn-lg px-5" id="submitBtn" style="display:none;">
                                    <i class="fas fa-check-circle me-2"></i>@lang('Create Trip')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Pricing Breakdown Modal --}}
<div id="pricingBreakdownModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Pricing Breakdown & Profitability')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="pricing-card p-4">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span>@lang('Current Ticket Price')</span>
                        <h4 class="text--primary mb-0">{{ gs('cur_sym') }}<span id="pb-final-price">0.00</span></h4>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            @lang('Base Price')
                            <span>{{ gs('cur_sym') }}<span id="pb-base-price">0.00</span></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            @lang('Surcharges (Weekend/Holiday)')
                            <span class="text--danger">+{{ gs('cur_sym') }}<span id="pb-surcharges">0.00</span></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            @lang('Discounts (Early Bird)')
                            <span class="text--success">-{{ gs('cur_sym') }}<span id="pb-discounts">0.00</span></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-top-0 pt-3">
                            <strong>@lang('Platform Commission') (<span id="pb-comm-rate">0</span>%)</strong>
                            <span class="text--danger">-{{ gs('cur_sym') }}<span id="pb-commission">0.00</span></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-light p-3 rounded mt-3">
                            <h6 class="mb-0">@lang('Net Revenue per Seat')</h6>
                            <h5 class="text--success mb-0">{{ gs('cur_sym') }}<span id="pb-net">0.00</span></h5>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

{{-- Seat Pricing Preview Modal --}}
<div id="seatPricingModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Seat-Level Pricing Map')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    <i class="las la-info-circle"></i> @lang('Visual preview of premiums applied to specific seats/rows based on active rules.')
                </div>
                <div id="seatPricingMap" class="text-center p-4">
                    {{-- Populated via AJAX --}}
                    <div class="spinner-border text--primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .wizard-progress {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }
    .wizard-progress .step {
        text-align: center;
        z-index: 2;
        background: #fff;
        padding: 0 10px;
    }
    .wizard-progress .step-line {
        flex-grow: 1;
        height: 2px;
        background: #e0e0e0;
        margin: 0 15px;
        position: relative;
        top: -10px;
        z-index: 1;
    }
    .wizard-progress .step.active .step-icon {
        background: #4634ff;
        color: #fff;
        border-color: #4634ff;
    }
    .wizard-progress .step.active .step-label {
        color: #4634ff;
        font-weight: 700;
    }
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 10px;
        color: #999;
        transition: all 0.3s;
    }
    .step-label {
        color: #999;
        font-size: 14px;
        font-weight: 600;
    }

    /* Inventory Cards */
    .inventory-card {
        cursor: pointer;
        width: 100%;
    }
    .inventory-card input:checked + .card-content {
        border-color: #4634ff;
        background: #f0f0ff;
    }
    .inventory-card input:checked + .card-content i {
        color: #4634ff !important;
    }
    .inventory-card .card-content {
        border: 2px solid #eaebed;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
    }

    /* Amenity Grid */
    .amenity-item {
        cursor: pointer;
        display: block;
        position: relative;
        width: 100%;
    }
    .amenity-item input { position: absolute; opacity: 0; z-index: -1; }
    .amenity-item .amenity-box {
        border: 1px solid #eaebed;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        transition: all 0.2s;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;
        background: #fff;
    }
    input:checked + .amenity-item .amenity-box {
        border-color: #4634ff;
        background: #f0f0ff;
        color: #4634ff;
    }
    .amenity-item .amenity-box i { font-size: 24px; }

    /* Policy Cards */
    .policy-card .card-content {
        border: 1px solid #eaebed;
        border-radius: 6px;
        padding: 15px;
        height: 100%;
    }

    /* Stops Timeline */
    .stops-timeline {
        position: relative;
        padding-left: 30px;
    }
    .stops-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -25px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4634ff;
        border: 2px solid #fff;
        z-index: 2;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        border-left: 3px solid #4634ff;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // ==========================================
        // 1. Wizard Navigation & Validation
        // ==========================================
        let currentStep = 1;
        const totalSteps = 3;

        function showStep(step) {
            $('.wizard-step').hide();
            $(`#step-${step}`).fadeIn(300);
            
            // Buttons
            step === 1 ? $('#prevBtn').hide() : $('#prevBtn').show();
            if(step === totalSteps) {
                $('#nextBtn').hide();
                $('#submitBtn').show();
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            }

            // Progress Bar
            $('.wizard-progress .step').removeClass('active');
            $(`.wizard-progress .step[data-step="${step}"]`).addClass('active');
            for(let i=1; i<step; i++) {
                $(`.wizard-progress .step[data-step="${i}"]`).addClass('active');
            }
        }

        $('#nextBtn').on('click', function() {
            // Step 1 Validation
            if(currentStep === 1) {
                if(!$('input[name="title"]').val() || 
                   !$('select[name="fleet_type"]').val() || 
                   !$('select[name="route"]').val() || 
                   !$('input[name="departure_datetime"]').val() || 
                   !$('input[name="arrival_datetime"]').val() || 
                   !$('select[name="vehicle_id"]').val()) {
                    notify('error', '@lang("Please fill all required fields")');
                    return;
                }
            }
            
            // Step 2 Validation
            if(currentStep === 2) {
                if(!$('input[name="base_price"]').val()) {
                    notify('error', '@lang("Base price is required")');
                    return;
                }
                // Partial Quota Validation
                if($('input[name="inventory_allocation"]:checked').val() === 'partial') {
                    const count = parseInt($('input[name="inventory_count"]').val()) || 0;
                    const minQuota = {{ $minB2CQuota ?? 0 }};
                    if(count < minQuota) {
                        notify('error', `@lang("Minimum B2C quota is") ${minQuota}`);
                        return;
                    }
                }
            }

            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        $('#prevBtn').on('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Initialize Select2
        $('.select2').select2();

        // ==========================================
        // 2. Route Logic & Swapping
        // ==========================================
        $('select[name="route"]').on('change', function() {
            updateRouteInfo();
        });

        $('#route_template_id').on('change', function() {
            loadTemplate($(this).val());
        });

        function loadTemplate(id) {
            if(!id) {
                $('.route-stops-wrapper').slideUp();
                return;
            }

            $.get(`{{ route('owner.route.builder.load', '') }}/${id}`, function(res) {
                if(res.id) {
                    // Update dates/time if possible or just show timeline
                    renderStopsTimeline(res.stops);
                    $('.route-stops-wrapper').slideDown();
                    
                    // Auto-select route if template has base_route
                    if(res.base_route_id) {
                        $('select[name="route"]').val(res.base_route_id).trigger('change');
                    }
                }
            });
        }

        function renderStopsTimeline(stops) {
            let html = '';
            stops.forEach((stop, index) => {
                html += `
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">${stop.city_name}</h6>
                                <span class="badge badge--pill badge--primary small">${index === 0 ? '@lang("Origin")' : (index === stops.length - 1 ? '@lang("Destination")' : stop.formatted_time_offset)}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="las la-clock"></i> ${stop.dwell_time_minutes}m @lang("Wait") 
                                <i class="las la-road ms-2"></i> ${stop.distance_from_previous}km @lang("from prev")
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#stopsTimeline').html(html);
        }

        function updateRouteInfo() {
            var selected = $('select[name="route"]').find('option:selected');
            var source = selected.attr('data-source');
            var dest = selected.attr('data-destination');
            var sourceId = selected.attr('data-source-id');
            var destId = selected.attr('data-destination-id');
            
            if(source && dest) {
                renderRouteVisual(source, dest);
                $('#route-from-id').val(sourceId);
                $('#route-to-id').val(destId);
            } else {
                $('.from-to-wrapper').slideUp().html('');
            }
        }

        function renderRouteVisual(source, dest) {
            var html = `
            <div class="route-visual-container p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                <div class="route-timeline d-flex align-items-center flex-grow-1">
                    <div class="point start">
                        <i class="fas fa-circle text--success"></i>
                        <span class="fw-bold ms-2">${source}</span>
                    </div>
                    <div class="line flex-grow-1 mx-3" style="height: 2px; background: #ccc; position: relative;">
                        <i class="fas fa-chevron-right" style="position: absolute; right: 50%; top: -8px; color: #999;"></i>
                    </div>
                    <div class="point end">
                        <span class="fw-bold me-2">${dest}</span>
                        <i class="fas fa-map-marker-alt text--danger"></i>
                    </div>
                </div>
            </div>`;
            $('.from-to-wrapper').html(html).slideDown();
        }

        // ==========================================
        // 3. Inventory & Fleet Logic
        // ==========================================
        $('input[name="inventory_allocation"]').on('change', function() {
            if($(this).val() === 'partial') {
                $('#inventory-count-wrapper').slideDown();
            } else {
                $('#inventory-count-wrapper').slideUp();
            }
        });

        $('select[name="fleet_type"]').on('change', function() {
            updateVehicleList();
        });

        function updateVehicleList() {
            var fleetTypeId = $('select[name="fleet_type"]').val();
            $('.vehicle-select option').prop('disabled', false); 
            
            if(fleetTypeId) {
                $('.vehicle-select option').each(function() {
                    var vType = $(this).data('fleet-type');
                    if(vType && vType != fleetTypeId && $(this).val() != "") {
                        $(this).prop('disabled', true);
                    }
                });
            }
            $('.vehicle-select').select2();
        }

        // ==========================================
        // 4. Date & Duration (Flatpickr)
        // ==========================================
        // Ensure flatpickr is loaded or use fallback
        if(typeof flatpickr !== 'undefined') {
            flatpickr("input[name='departure_datetime']", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                onChange: calculateDuration
            });
            flatpickr("input[name='arrival_datetime']", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                onChange: calculateDuration
            });
        } else {
            // Fallback for native input change
            $('input[name="departure_datetime"], input[name="arrival_datetime"]').on('change', calculateDuration);
        }

        function calculateDuration() {
            var depStr = $('input[name="departure_datetime"]').val();
            var arrStr = $('input[name="arrival_datetime"]').val();
            
            if(depStr && arrStr) {
                var dep = new Date(depStr);
                var arr = new Date(arrStr);
                var diff = arr - dep;
                
                if(diff > 0) {
                    var hours = Math.floor(diff / 36e5);
                    var mins = Math.floor((diff % 36e5) / 6e4);
                    $('#duration-display').text(`@lang("Duration"): ${hours}h ${mins}m`);
                } else {
                    $('#duration-display').text('@lang("Invalid: Arrival must be after Departure")');
                }
            }
        }

        // ==========================================
        // 5. Pricing Preview (Real-time)
        // ==========================================
        const pricingInputs = 'input[name="base_price"], input[name="weekend_surcharge"], input[name="holiday_surcharge"], input[name="early_bird_discount"], input[name="last_minute_surcharge"]';
        
        $(document).on('input change', pricingInputs, function() {
           // Debounce or just call calc? Let's just do calc for now.
           updatePricingPreview();
        });

        function updatePricingPreview() {
            let basePrice = parseFloat($('input[name="base_price"]').val()) || 0;
            let weekend = parseFloat($('input[name="weekend_surcharge"]').val()) || 0;
            let holiday = parseFloat($('input[name="holiday_surcharge"]').val()) || 0;
            let early = parseFloat($('input[name="early_bird_discount"]').val()) || 0;

            let price = basePrice;
            price += price * (weekend/100);
            price += price * (holiday/100);
            price -= price * (early/100);

            $('#preview-final-price').text(price.toFixed(2));
            
            // Shared logic for Modal too
            $('#pb-base-price').text(basePrice.toFixed(2));
            $('#pb-final-price').text(price.toFixed(2));
            $('#pb-surcharges').text((basePrice * ((weekend + holiday)/100)).toFixed(2));
            $('#pb-discounts').text((basePrice * (early/100)).toFixed(2));
        }

        $('.pricingBreakdownBtn').on('click', function() {
            const formData = $('#tripWizardForm').serialize();
            $.get(`{{ route('owner.trip.pricing.preview', '') }}`, formData, function(res) {
                if(res.status === 'success') {
                    const data = res.data;
                    $('#pb-base-price').text(data.base_price.toFixed(2));
                    $('#pb-final-price').text(data.final_price.toFixed(2));
                    $('#pb-comm-rate').text(data.commission_rate);
                    $('#pb-commission').text(data.commission_per_booking.toFixed(2));
                    $('#pb-net').text(data.net_revenue_per_booking.toFixed(2));
                    $('#pb-surcharges').text((data.final_price - data.base_price + (data.base_price * ($('input[name="early_bird_discount"]').val() || 0)/100)).toFixed(2));
                    $('#pb-discounts').text((data.base_price * ($('input[name="early_bird_discount"]').val() || 0)/100).toFixed(2));
                    $('#pricingBreakdownModal').modal('show');
                }
            });
        });

        $('.suggestPriceBtn').on('click', function() {
            const tripId = `{{ @$trip->id ?: 0 }}`;
            const routeId = $('select[name="route"]').val();
            if(!routeId) {
                notify('error', '@lang("Please select a route first")');
                return;
            }

            $(this).find('i').addClass('fa-spin');
            
            $.get(`{{ route('owner.trip.pricing.suggest', '') }}`, {route_id: routeId}, (res) => {
                $(this).find('i').removeClass('fa-spin');
                if(res.status === 'success') {
                    $('#base_price').val(res.data.suggested_price).trigger('input');
                    notify('success', `@lang("Smart Pricing applied"): ${res.data.reason}`);
                }
            });
        });

        $('.seatPricingPreviewBtn').on('click', function() {
            const modal = $('#seatPricingModal');
            modal.modal('show');
            $('#seatPricingMap').html('<div class="spinner-border text--primary" role="status"></div>');
            
            $.get(`{{ route('owner.seat.pricing.preview', @$trip->id ?: 0) }}`, function(res) {
                if(res.html) {
                    $('#seatPricingMap').html(res.html);
                } else if(res.data) {
                    // Fallback render if server returns JSON
                    $('#seatPricingMap').html('<p class="text-muted">@lang("Modifier map loaded. Rows with premiums are highlighted.")</p>');
                } else {
                    $('#seatPricingMap').html('<p class="text-muted">@lang("No seat-specific premiums found for this trip.")</p>');
                }
            });
        });

        // ==========================================
        // 6. Vehicle Amenities (Legacy)
        // ==========================================
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
            if(!vehicleId) {
                $('#inheritedAmenitiesDisplay').hide();
                $('#noVehicleSelectedMessage').show();
                return;
            }

            const vehicle = vehiclesData.find(v => v.id == vehicleId);
            if(!vehicle || !vehicle.amenities || vehicle.amenities.length === 0) {
                $('#inheritedAmenitiesDisplay').hide();
                $('#noVehicleSelectedMessage').html(
                    '<i class="las la-info-circle me-2"></i> @lang("This vehicle has no built-in amenities")'
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

        @if(@$trip && @$trip->vehicle_id)
            $('select[name="vehicle_id"]').trigger('change');
        @endif
        
        // Initial Runs
        showStep(1);
        if($('select[name="route"]').val()) {
            updateRouteInfo();
        }
        updateVehicleList();

    })(jQuery);

</script>
@endpush
