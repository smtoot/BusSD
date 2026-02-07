@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-lg-8">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">
                                    @lang('Welcome to') <strong>{{ __(gs('site_name')) }}</strong>
                                </h3>
                                <p class="text-white">{{ __($pageTitle) }}</p>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('owner.register') }}" method="POST"
                                    class="cmn-form mt-30 verify-gcaptcha login-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6 form-group">
                                            <label>@lang('First Name')</label>
                                            <input type="text" class="form-control" value="{{ old('firstname') }}"
                                                name="firstname" required>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>@lang('Last Name')</label>
                                            <input type="text" class="form-control" value="{{ old('lastname') }}"
                                                name="lastname" required>
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            <label>@lang('E-Mail Address')</label>
                                            <input type="email" class="form-control checkUser" value="{{ old('email') }}"
                                                name="email" required>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>@lang('Password')</label>
                                            <div>
                                                <input type="password"
                                                    class="form-control @if (gs('secure_password')) secure-password @endif"
                                                    name="password" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>@lang('Confirm Password')</label>
                                            <input type="password" class="form-control" name="password_confirmation"
                                                required>
                                        </div>
                                    </div>
                                    @if (gs('agree'))
                                        @php
                                            $policyPages = getContent('policy_pages.element', false, orderById: true);
                                        @endphp
                                        <div>
                                            <input type="checkbox" id="agree" @checked(old('agree'))
                                                name="agree" required>
                                            <label for="agree">@lang('I agree with')</label> <span>
                                                @foreach ($policyPages as $policy)
                                                    <a href="{{ route('policy.pages', $policy->slug) }}" class="forget-text"
                                                        target="_blank">{{ __($policy->data_values->title) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span>
                                        </div>
                                    @endif

                                    <x-captcha />

                                    <button type="submit" class="btn cmn-btn w-100">@lang('REGISTER')</button>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('owner.login') }}" class="text-white">
                                            @lang('Login') <i class="las la-sign-out-alt"></i>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="existModalCenter">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('owner.login') }}" class="btn btn--base btn-sm">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('owner.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
