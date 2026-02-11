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
                                                    data-source="{{ $route->startingPoint->name }}"
                                                    data-source-id="{{ $route->starting_point }}"
                                                    data-destination="{{ $route->destinationPoint->name }}"
                                                    data-destination-id="{{ $route->destination_point }}"
                                                    @selected(old('route', @$trip->route_id) == $route->id)>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Hidden Route Points -->
                            <input type="hidden" name="from" id="route-from-id" value="{{ old('from', @$trip->starting_point) }}">
                            <input type="hidden" name="to" id="route-to-id" value="{{ old('to', @$trip->destination_point) }}">

                            <!-- Route Visual & Swap (Hidden initially) -->
                            <div class="col-12 from-to-wrapper" style="display:none;"></div>

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
                                        <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price', @$trip->seat_price) }}" required>
                                    </div>
                                    <small class="text-muted">@lang('Main route price (Source to Destination)')</small>
                                </div>
                            </div>
                            
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
    .policy-card {
        cursor: pointer;
        display: block;
        position: relative;
        width: 100%;
        height: 100%;
    }
    input:checked + .policy-card .card-content {
        border-color: #4634ff;
        background: #fff8f8; /* Distinct from inventory */
    }
    .policy-card .card-content {
        border: 1px solid #eaebed;
        border-radius: 6px;
        padding: 15px;
        height: 100%;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Wizard navigation
        let currentStep = 1;
        const totalSteps = 3;

        function showStep(step) {
            $('.wizard-step').hide();
            $(`#step-${step}`).fadeIn(300);
            
            // Update buttons
            if(step === 1) {
                $('#prevBtn').hide();
            } else {
                $('#prevBtn').show();
            }
            
            if(step === totalSteps) {
                $('#nextBtn').hide();
                $('#submitBtn').show();
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            }

            // Update Progress
            $('.wizard-progress .step').removeClass('active');
            $(`.wizard-progress .step[data-step="${step}"]`).addClass('active');
            for(let i=1; i<step; i++) {
                $(`.wizard-progress .step[data-step="${i}"]`).addClass('active'); // Keep previous active
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

        // Inventory toggle
        $('input[name="inventory_allocation"]').on('change', function() {
            if($(this).val() === 'partial') {
                $('#inventory-count-wrapper').slideDown();
            } else {
                $('#inventory-count-wrapper').slideUp();
            }
        });

        // Fleet/Route Logic (Adapted from old form)
        $('select[name="fleet_type"]').on('change', function() {
            updateVehicleList();
        });

        function updateRouteInfo() {
            var selected = $('select[name="route"]').find('option:selected');
            var source = selected.attr('data-source');
            var dest = selected.attr('data-destination');
            var sourceId = selected.attr('data-source-id');
            var destId = selected.attr('data-destination-id');
            
            if(source && dest) {
                var html = `
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div><strong>@lang('Route:'):</strong> ${source} <i class="fas fa-arrow-right mx-2"></i> ${dest}</div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="swapRouteBtn"><i class="las la-exchange-alt"></i> @lang('Swap')</button>
                </div>`;
                $('.from-to-wrapper').html(html).slideDown();
                
                // Update hidden inputs
                $('#route-from-id').val(sourceId);
                $('#route-to-id').val(destId);
            } else {
                $('.from-to-wrapper').slideUp().html('');
            }
        }

        $('select[name="route"]').on('change', function() {
            updateRouteInfo();
        });

        // Initialize state on load
        showStep(1);
        if($('select[name="route"]').val()) {
            updateRouteInfo();
        }
        updateVehicleList();

        // Vehicle Filter
        function updateVehicleList() {
            var fleetTypeId = $('select[name="fleet_type"]').val();
            $('.vehicle-select option').prop('disabled', false); // Reset
            
            if(fleetTypeId) {
                $('.vehicle-select option').each(function() {
                    var vType = $(this).data('fleet-type');
                    if(vType && vType != fleetTypeId && $(this).val() != "") {
                        $(this).prop('disabled', true);
                    }
                });
            }
            $('.vehicle-select').select2(); // Refresh
        }

        // Duration Calculator
        $('input[name="departure_datetime"], input[name="arrival_datetime"]').on('change', function() {
            var depStr = $('input[name="departure_datetime"]').val();
            var arrStr = $('input[name="arrival_datetime"]').val();
            
            if(depStr && arrStr) {
                var dep = new Date(depStr);
                var arr = new Date(arrStr);
                var diff = arr - dep;
                
                if(diff > 0) {
                    var hours = Math.floor(diff / 36e5);
                    var mins = Math.floor((diff % 36e5) / 6e4);
                    $('#duration-display').text(`Duration: ${hours}h ${mins}m`);
                } else {
                    $('#duration-display').text('Invalid: Arrival must be after Departure');
                }
            }
        });

        // Vehicle Amenities Display Logic
        const vehiclesData = @json($vehicles->map(function($v) {
            return [
                'id' => $v->id,
                'amenities' => $v->amenities->map(function($a) {
                    return ['id' => $a->id, 'label' => $a->label, 'icon' => $a->icon];
                })
            ];
        }));

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

    })(jQuery);

</script>
@endpush
