@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('owner.counter.manager.store', @$user->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Image Uploader Section -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ @$user->image }}" class="w-100" type="counter_manager"
                                        :required="false" />
                                </div>
                            </div>

                            <!-- Form Section -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('First Name')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ old('firstname', @$user->firstname) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">@lang('Last Name')</label>
                                            <input class="form-control" type="text" name="lastname"
                                                value="{{ old('lastname', @$user->lastname) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">@lang('Username')</label>
                                            @if (@$user)
                                                <input class="form-control" type="text" value="{{ $user->username }}"
                                                    disabled>
                                            @else
                                                <input class="form-control" type="text" name="username"
                                                    value="{{ old('username') }}" required>
                                                <small class="text--danger">
                                                    <i class="las la-info-circle"></i>
                                                    <i>@lang('Username must not be less than 6 characters.')</i>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email')</label>
                                            @if (@$user)
                                                <input class="form-control" type="email" value="{{ $user->email }}"
                                                    disabled>
                                            @else
                                                <input class="form-control" type="email" name="email"
                                                    value="{{ old('email') }}" required>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Mobile Number')</label>
                                            <div class="input-group">
                                                <span class="input-group-text mobile-code">+249</span>
                                                <input type="hidden" name="mobile_code" value="249">
                                                <input type="hidden" name="country_code" value="SD">
                                                <input type="number" name="mobile"
                                                    value="{{ old('mobile', @$user->mobile) }}"
                                                    class="form-control checkUser" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Address')</label>
                                            <input class="form-control" type="text" name="address"
                                                value="{{ old('address', @$user->address) }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group">
                                            <label>@lang('City')</label>
                                            <input class="form-control" type="text" name="city"
                                                value="{{ old('city', @$user->city) }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group">
                                            <label>@lang('State')</label>
                                            <input class="form-control" type="text" name="state"
                                                value="{{ old('state', @$user->state) }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Zip/Postal')</label>
                                            <input class="form-control" type="text" name="zip"
                                                value="{{ old('zip', @$user->zip) }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
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


                                    <div class="col-md-12">
                                        <hr>
                                        <h6 class="mb-3">@lang('Permissions') (Optional - Overrides Default)</h6>
                                        <div class="row">
                                            @php
                                                $permissionList = [
                                                    'fleet_management' => 'Fleet Management',
                                                    'staff_management' => 'Staff Management',
                                                    'trip_management' => 'Trip Management',
                                                    'ticket_management' => 'Ticket Management',
                                                    'booking_management' => 'Booking Management',
                                                    'sales_reports' => 'Sales Reports',
                                                    'financial_management' => 'Financial Management',
                                                    'boarding_management' => 'Boarding Management',
                                                ];
                                            @endphp
                                            @foreach ($permissionList as $key => $val)
                                                <div class="col-xl-3 col-lg-4 col-sm-6 mb-3">
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

                                    <div class="col-md-12">
                                        <button type="submit"
                                            class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if (@$user->id && auth()->guard('owner')->user())
        <a href="{{ route('owner.counter.manager.login', @$user->id) }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-sign-in-alt"></i>@lang('Login as Counter Manager')
        </a>
    @endif
    <x-back route="{{ route('owner.counter.manager.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const mobileCode = '{{ @$mobileCode }}';
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
