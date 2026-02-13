@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-12 mb-30">
            <form action="{{ route('owner.driver.store', @$user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Personal Information Segment --}}
                <div class="card mb-4">
                    <div class="card-header bg--primary">
                        <h5 class="text-white"><i class="las la-user"></i> @lang('Personal Information')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Driver\'s Full Name')</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ old('firstname', @$user->firstname) }}" placeholder="@lang('First Name')" required>
                                        </div>
                                        <div class="col-6">
                                            <input class="form-control" type="text" name="lastname"
                                                value="{{ old('lastname', @$user->lastname) }}" placeholder="@lang('Last Name')" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Phone Number')</label>
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code">+249</span>
                                        <input type="hidden" name="mobile_code" value="249">
                                        <input type="hidden" name="country_code" value="SD">
                                        <input type="number" name="mobile"
                                            value="{{ old('mobile', @$user->mobile) }}"
                                            class="form-control checkUser" placeholder="@lang('Mobile Number')" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Nationality')</label>
                                    <select name="nationality" class="form-control select2" required>
                                        @foreach ($countries as $key => $country)
                                            <option value="{{ $country->country }}"
                                                @selected(old('nationality', @$user->nationality ?? 'Sudan') == $country->country)>
                                                {{ __($country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('ID Type')</label>
                                    <select name="id_type" class="form-control" required>
                                        <option value="ID Card" @selected(old('id_type', @$user->id_type) == 'ID Card')>@lang('ID Card')</option>
                                        <option value="Passport" @selected(old('id_type', @$user->id_type) == 'Passport')>@lang('Passport')</option>
                                        <option value="Driving License" @selected(old('id_type', @$user->id_type) == 'Driving License')>@lang('Driving License')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('ID Number')</label>
                                    <input class="form-control" type="text" name="id_number"
                                        value="{{ old('id_number', @$user->id_number) }}" placeholder="@lang('Enter ID Number')" required>
                                </div>
                            </div>

                            @if(!@$user)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input class="form-control" type="text" name="username"
                                        value="{{ old('username') }}" required>
                                    <small class="text--info">@lang('Min 6 characters')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" type="email" name="email"
                                        value="{{ old('email') }}" required>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- License & Official Documents Segment --}}
                <div class="card mb-4">
                    <div class="card-header bg--success">
                        <h5 class="text-white"><i class="las la-file-contract"></i> @lang('License & Official Documents')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Driver\'s License Number')</label>
                                    <input class="form-control" type="text" name="license_number"
                                        value="{{ old('license_number', @$user->license_number) }}" placeholder="@lang('LIC123456789')" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('License Expiry Date')</label>
                                    <input class="form-control datepicker-here" type="text" name="license_expiry_date"
                                        value="{{ old('license_expiry_date', @$user->license_expiry_date ? showDateTime($user->license_expiry_date, 'Y-m-d') : '') }}" 
                                        data-language="en" data-date-format="yyyy-mm-dd" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Driver\'s Photo')</label>
                                    <x-image-uploader image="{{ @$user->image }}" class="w-100" type="driver"
                                        :required="false" />
                                    <small class="text-muted">@lang('Click to upload or drag and drop (PNG, JPG up to 2MB)')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Details (City, State, etc.) Segment --}}
                <div class="card mb-4">
                    <div class="card-header bg--dark">
                        <h5 class="text-white"><i class="las la-map-marker-alt"></i> @lang('Address & Location Details')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address"
                                        value="{{ old('address', @$user->address) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city"
                                        value="{{ old('city', @$user->city) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state"
                                        value="{{ old('state', @$user->state) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip"
                                        value="{{ old('zip', @$user->zip) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Country')</label>
                                    <select name="country" class="form-control select2" required>
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}"
                                                value="{{ $country->country }}" data-code="{{ $key }}"
                                                @selected(old('country', @$user->country_name) == $country->country)>
                                                {{ __($country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Permissions Segment --}}
                <div class="card mb-4">
                    <div class="card-header bg--info">
                        <h5 class="text-white"><i class="las la-shield-alt"></i> @lang('Driver Operations Permissions')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $permissionList = [
                                    'trip_manifest' => 'View Trip Manifest',
                                    'passenger_checkin' => 'Passenger Check-in',
                                ];
                            @endphp
                            @foreach ($permissionList as $key => $val)
                                <div class="col-xl-6 col-lg-6 col-sm-6 mb-3">
                                    <div class="form-check form-check-inline custom--check">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                            id="{{ $key }}" class="form-check-input"
                                            @checked($user->hasPermission($key))>
                                        <label class="form-check-label"
                                            for="{{ $key }}">@lang($val)</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Driver')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if (@$user->id && auth()->guard('owner')->user())
        <a href="{{ route('owner.driver.login', @$user->id) }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-sign-in-alt"></i>@lang('Login as Driver')
        </a>
    @endif
    <x-back route="{{ route('owner.driver.index') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const mobileCode = '{{ @$mobileCode }}';
            // Default to Sudan (SD) if no mobile code is provided
            const defaultCountry = 'SD';
            
            if (mobileCode) {
                $(`option[data-code=${mobileCode}]`).attr('selected', '');
            } else if (!'{{ @$user->id }}') {
                $(`option[data-code=${defaultCountry}]`).attr('selected', '');
            }

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            // Initial trigger to sync mobile code display
            if($('select[name=country] :selected').length){
                 $('select[name=country]').trigger('change');
            } else if (!'{{ @$user->id }}') {
                // If adding new, select Sudan by default
                $('select[name=country]').val('Sudan').trigger('change');
            }

        })(jQuery)
    </script>
@endpush
