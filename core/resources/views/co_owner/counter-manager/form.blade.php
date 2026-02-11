@extends('co_owner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('co-owner.counter.manager.store', @$user->id) }}" method="POST">
                        @csrf
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
                                            <i>@lang('Username must not be less then 6 character.')</i>
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') </label>
                                    @if (@$user)
                                        <input class="form-control" type="email" value="{{ $user->email }}" disabled>
                                    @else
                                        <input class="form-control" type="email" name="email"
                                            value="{{ old('email') }}" required>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+249</span>
                                        <input type="hidden" name="mobile_code" value="249">
                                        <input type="hidden" name="country_code" value="SD">
                                        <input type="number" name="mobile" value="{{ old('mobile', @$user->mobile) }}"
                                            class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
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
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state"
                                        value="{{ old('state', @$user->state) }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip"
                                        value="{{ old('zip', @$user->zip) }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
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
                            @if (!@$user)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>@lang('Password')</label>
                                            <small class="text--warning">
                                                <i class="las la-info-circle"></i> <i>@lang('Default "123456"')</i>
                                            </small>
                                        </div>
                                        <input class="form-control" type="password" name="password" value="123456">
                                        <small class="text--danger">
                                            <i class="las la-info-circle"></i>
                                            <i>@lang('Password must not be less then 6 character.')</i>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Confirm Password')</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            value="123456">
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('co-owner.counter.manager.index') }}" />
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
