@extends('co_owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Recover Account')</h3>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('co-owner.password.update') }}" method="POST" class="login-form">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <div>
                                            <input type="password" name="password" class="form-control @if (gs('secure_password')) secure-password @endif" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('Confirm Password')</label>
                                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                                    </div>
                                    <button type="submit" class="btn cmn-btn w-100">@lang('Submit')</button>
                                    <div class="text-center mt-4">
                                        <a href="{{ route('co-owner.login') }}" class="text-white">
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

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
