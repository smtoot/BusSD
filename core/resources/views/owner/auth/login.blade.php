@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Welcome to') <strong>{{ __(gs('site_name')) }}</strong>
                                </h3>
                                <p class="text-white">{{ __($pageTitle) }}</p>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('owner.login') }}" method="POST"
                                    class="cmn-form mt-30 verify-gcaptcha login-form">
                                    @csrf
                                    <div class="form-group">
                                        <label>@lang('Username')</label>
                                        <input type="text" class="form-control" value="{{ old('username') }}"
                                            name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>@lang('Password')</label>
                                            <a href="{{ route('owner.password.request') }}" class="forget-text">
                                                @lang('Forgot Password?')
                                            </a>
                                        </div>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>

                                    <x-captcha />

                                    <button type="submit" class="btn cmn-btn w-100">@lang('LOGIN')</button>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('owner.register') }}" class="text-white">
                                            @lang('Create Account') <i class="las la-sign-out-alt"></i>
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
@endsection
