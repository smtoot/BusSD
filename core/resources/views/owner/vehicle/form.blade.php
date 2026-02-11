@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('owner.vehicle.store', @$vehicle->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Basic Information --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text--primary"><i class="las la-info-circle"></i> @lang('Basic Information')</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Vehicle Nickname') <span class="text-danger">*</span></label>
                                        <input type="text" name="nick_name" class="form-control" value="{{ old('nick_name', @$vehicle->nick_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Vehicle Type') <span class="text-danger">*</span></label>
                                        <select class="form-control" name="fleet_type" required>
                                            <option value="">@lang('Select One')</option>
                                            @foreach ($fleetTypes as $fleetType)
                                                <option value="{{ $fleetType->id }}" @selected(old('fleet_type', @$vehicle->fleet_type_id) == $fleetType->id)>{{ $fleetType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Brand') <span class="text-danger">*</span></label>
                                        <input type="text" name="brand_name" class="form-control" value="{{ old('brand_name', @$vehicle->brand_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Model') <span class="text-danger">*</span></label>
                                        <input type="text" name="model_no" class="form-control" value="{{ old('model_no', @$vehicle->model_no) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Regulatory & Official Details --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text--primary"><i class="las la-file-alt"></i> @lang('Regulatory & Official Details')</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('License Plate Number') <span class="text-danger">*</span></label>
                                        <input type="text" name="registration_no" class="form-control" value="{{ old('registration_no', @$vehicle->registration_no) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Insurance Policy Number')</label>
                                        <input type="text" name="insurance_policy_number" class="form-control" value="{{ old('insurance_policy_number', @$vehicle->insurance_policy_number) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Engine Number') <span class="text-danger">*</span></label>
                                        <input type="text" name="engine_no" class="form-control" value="{{ old('engine_no', @$vehicle->engine_no) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Chassis Number') <span class="text-danger">*</span></label>
                                        <input type="text" name="chasis_no" class="form-control" value="{{ old('chasis_no', @$vehicle->chasis_no) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Owner Name') <span class="text-danger">*</span></label>
                                        <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', @$vehicle->owner_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Owner Phone') <span class="text-danger">*</span></label>
                                        <input type="text" name="owner_phone" class="form-control" value="{{ old('owner_phone', @$vehicle->owner_phone) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Year of Manufacture')</label>
                                        <select class="form-control" name="year_of_manufacture">
                                            <option value="">@lang('Select Year')</option>
                                            @for($year = date('Y') + 1; $year >= 1990; $year--)
                                                <option value="{{ $year }}" @selected(old('year_of_manufacture', @$vehicle->year_of_manufacture) == $year)>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Capacity & Seating --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text--primary"><i class="las la-users"></i> @lang('Capacity & Seating')</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Total Number of Seats')</label>
                                        <input type="number" name="total_seats" class="form-control" min="1" max="100" value="{{ old('total_seats', @$vehicle->total_seats) }}">
                                        <small class="text-muted">@lang('Leave empty to use fleet type default')</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="d-block mb-2">@lang('Vehicle Category')</label>
                                        <div class="custom-control custom-checkbox">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_vip" value="1" id="isVipCheckbox" @checked(old('is_vip', @$vehicle->is_vip))>
                                                <label class="form-check-label" for="isVipCheckbox">
                                                    @lang('This is a VIP bus')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Fixed Amenities & Features --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text--primary"><i class="las la-star"></i> @lang('Fixed Amenities & Features')</h5>
                            <p class="text-muted small mb-3">@lang('Select the permanent features built into this vehicle')</p>
                            <div class="row g-3">
                                @forelse ($amenities as $amenity)
                                    <div class="col-md-4 col-lg-3">
                                        <div class="custom-control custom-checkbox bg-light p-3 rounded border">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                    name="amenities[]" 
                                                    value="{{ $amenity->id }}" 
                                                    class="form-check-input amenity-checkbox" 
                                                    id="amenity{{ $amenity->id }}"
                                                    @if(@$vehicle && $vehicle->amenities->contains($amenity->id)) checked @endif
                                                    >
                                                <label class="form-check-label d-flex align-items-center" for="amenity{{ $amenity->id }}">
                                                    <i class="{{ $amenity->icon }} me-2 fs-5"></i>
                                                    {{ __($amenity->label) }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning">@lang('No vehicle amenities found. Please contact admin.')</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="form-group text-end mt-4">
                            <button type="submit" class="btn btn--primary btn-lg w-100">
                                <i class="las la-save"></i> @lang('Save Vehicle Information')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.vehicle.index') }}" class="btn btn-sm btn--primary">
        <i class="la la-fw la-backward"></i> @lang('Go Back')
    </a>
@endpush
