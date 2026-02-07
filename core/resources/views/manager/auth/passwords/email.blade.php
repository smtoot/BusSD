@extends('manager.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">{{ __($pageTitle) }}</h3>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('manager.password.email') }}" method="POST" class="login-form verify-gcaptcha">
                                    @csrf
                                    <div class="form-group">
                                        <label>@lang('Email Or Username')</label>
                                        <input type="text" name="value" class="form-control" value="{{ old('value') }}" required>
                                    </div>
                                    <x-captcha />
                                    <button type="submit" class="btn cmn-btn w-100">@lang('Submit')</button>
                                    <div class="text-center mt-4">
                                        <a href="{{ route('manager.login') }}" class="text-white">
                                            <i class="las la-sign-out-alt"></i> @lang('Back to Login')
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
