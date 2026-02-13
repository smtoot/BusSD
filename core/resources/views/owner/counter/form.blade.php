@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                </div>
                <form method="POST" action="{{ isset($branch) ? route('owner.counter.update', $branch->id) : route('owner.counter.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-6">
                                <h6 class="mb-3">@lang('Basic Information')</h6>
                                
                                <div class="form-group">
                                    <label>@lang('Branch Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name ?? '') }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Branch Type') <span class="text-danger">*</span></label>
                                            <select name="type" class="form-control">
                                                <option value="branch" {{ old('type', $branch->type ?? 'branch') == 'branch' ? 'selected' : '' }}>@lang('Branch')</option>
                                                <option value="headquarters" {{ old('type', $branch->type ?? '') == 'headquarters' ? 'selected' : '' }}>@lang('Headquarters')</option>
                                                <option value="sub_branch" {{ old('type', $branch->type ?? '') == 'sub_branch' ? 'selected' : '' }}>@lang('Sub Branch')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Autonomy Level') <span class="text-danger">*</span></label>
                                            <select name="autonomy_level" class="form-control">
                                                <option value="controlled" {{ old('autonomy_level', $branch->autonomy_level ?? 'controlled') == 'controlled' ? 'selected' : '' }}>@lang('Controlled')</option>
                                                <option value="semi_autonomous" {{ old('autonomy_level', $branch->autonomy_level ?? '') == 'semi_autonomous' ? 'selected' : '' }}>@lang('Semi Autonomous')</option>
                                                <option value="autonomous" {{ old('autonomy_level', $branch->autonomy_level ?? '') == 'autonomous' ? 'selected' : '' }}>@lang('Autonomous')</option>
                                            </select>
                                            <small class="text-muted">@lang('Controls how much independence this branch has')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('City') <span class="text-danger">*</span></label>
                                    <select name="city_id" class="form-control select2" required>
                                        <option value="">@lang('Select City')</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $branch->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ __($city->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Address / Location')</label>
                                    <textarea class="form-control" name="location" rows="3">{{ old('location', $branch->location ?? '') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Timezone')</label>
                                    <select name="timezone" class="form-control select2">
                                        <option value="">@lang('Default Timezone')</option>
                                        @foreach($timezones as $timezone)
                                            <option value="{{ $timezone }}" {{ old('timezone', $branch->timezone ?? '') == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">@lang('Leave empty to use system default')</small>
                                </div>

                                <hr class="my-4">
                                <h6 class="mb-3">@lang('Contact Information')</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Mobile Number') <span class="text-danger">*</span></label>
                                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $branch->mobile ?? '') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email Address')</label>
                                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $branch->contact_email ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Branch Manager')</label>
                                    <select class="select2 form-control" name="counter_manager">
                                        <option value="0">@lang('None / Assign Later')</option>
                                        @foreach ($counterManagers as $counterManager)
                                            <option value="{{ $counterManager->id }}" {{ old('counter_manager', $branch->counter_manager_id ?? 0) == $counterManager->id ? 'selected' : '' }}>
                                                {{ $counterManager->fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">@lang('Person responsible for this branch')</small>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-6">
                                <h6 class="mb-3">@lang('Operational Settings')</h6>

                                <!-- Booking Settings -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3"><i class="las la-ticket-alt"></i> @lang('Booking Permissions')</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="allows_online_booking" id="allows_online_booking" value="1" 
                                                {{ old('allows_online_booking', $branch->allows_online_booking ?? 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="allows_online_booking">
                                                <strong>@lang('Allow Online Booking')</strong>
                                                <br><small class="text-muted">@lang('Customers can book tickets online for trips from this branch')</small>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allows_counter_booking" id="allows_counter_booking" value="1"
                                                {{ old('allows_counter_booking', $branch->allows_counter_booking ?? 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="allows_counter_booking">
                                                <strong>@lang('Allow Counter Booking')</strong>
                                                <br><small class="text-muted">@lang('Staff can book tickets at the counter')</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Controls -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3"><i class="las la-dollar-sign"></i> @lang('Pricing Control')</h6>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="can_adjust_pricing" id="can_adjust_pricing" value="1"
                                                {{ old('can_adjust_pricing', $branch->can_adjust_pricing ?? 0) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="can_adjust_pricing">
                                                <strong>@lang('Can Adjust Ticket Pricing')</strong>
                                                <br><small class="text-muted">@lang('Branch can modify base ticket prices')</small>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Maximum Pricing Variance (%)')</label>
                                            <input type="number" name="pricing_variance_limit" class="form-control" 
                                                value="{{ old('pricing_variance_limit', $branch->pricing_variance_limit ?? 0) }}" 
                                                min="0" max="100">
                                            <small class="text-muted">@lang('Maximum % this branch can adjust prices (0-100)')</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Route Controls -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3"><i class="las la-route"></i> @lang('Route Management')</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_set_routes" id="can_set_routes" value="1"
                                                {{ old('can_set_routes', $branch->can_set_routes ?? 0) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="can_set_routes">
                                                <strong>@lang('Can Create Custom Routes')</strong>
                                                <br><small class="text-muted">@lang('Branch can define its own routes and schedules')</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Business Registration -->
                                <hr class="my-4">
                                <h6 class="mb-3">@lang('Business Registration (Optional)')</h6>

                                <div class="form-group">
                                    <label>@lang('Tax Registration Number')</label>
                                    <input type="text" name="tax_registration_no" class="form-control" 
                                        value="{{ old('tax_registration_no', $branch->tax_registration_no ?? '') }}">
                                    <small class="text-muted">@lang('Government tax ID for this branch')</small>
                                </div>

                                <!-- Bank Account Details -->
                                <div class="card border mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="las la-university"></i> @lang('Bank Account Details')</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>@lang('Account Holder Name')</label>
                                            <input type="text" name="bank_account_name" class="form-control" 
                                                value="{{ old('bank_account_name', isset($branch->bank_account_details['name']) ? $branch->bank_account_details['name'] : '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Account Number')</label>
                                            <input type="text" name="bank_account_number" class="form-control" 
                                                value="{{ old('bank_account_number', isset($branch->bank_account_details['number']) ? $branch->bank_account_details['number'] : '') }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('Bank Name')</label>
                                                    <input type="text" name="bank_name" class="form-control" 
                                                        value="{{ old('bank_name', isset($branch->bank_account_details['bank']) ? $branch->bank_account_details['bank'] : '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('IBAN / Routing Number')</label>
                                                    <input type="text" name="bank_iban" class="form-control" 
                                                        value="{{ old('bank_iban', isset($branch->bank_account_details['iban']) ? $branch->bank_account_details['iban'] : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">@lang('For revenue settlements and transfers')</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('owner.counter.index') }}" class="btn btn-outline-secondary">
                                    <i class="las la-times"></i> @lang('Cancel')
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn--primary">
                                    <i class="las la-save"></i> {{ isset($branch) ? __('Update Branch') : __('Create Branch') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.counter.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-list"></i> @lang('All Branches')
    </a>
@endpush
