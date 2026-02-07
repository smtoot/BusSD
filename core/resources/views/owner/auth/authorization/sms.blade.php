@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container d-flex justify-content-center">
            <div class="login-area">
                <div class="text-center mb-3">
                    <h2 class="text-white mb-2">{{ __($pageTitle) }}</h2>
                    <p class="text-white mb-2">
                        @lang('A 6 digit verification code sent to your mobile number') :  +{{ showMobileNumber(authUser()->mobileNumber) }}
                    </p>
                </div>
                <form action="{{ route('owner.verify.mobile') }}" method="POST" class="login-form w-100">
                    @csrf

                    @include('owner.partials.verification_code')

                    <button type="submit" class="btn cmn-btn w-100">@lang('Submit')</button>
                    <div class="mt-3">
                        <p class="text-white">
                            @lang('Don\'t get any code'), <span class="countdown-wrapper text-white">@lang('try again after') <span id="countdown" class="fw-bold text-white">--</span> @lang('seconds')</span> <a href="{{route('owner.send.verify.code', 'sms')}}" class="try-again-link forget-text d-none"> @lang('Try again')</a>
                        </p>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('owner.logout') }}" class="text-white"><i class="las la-sign-out-alt"></i> @lang('Logout')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';
            var distance =Number("{{@$owner->ver_code_send_at->addMinutes(2)->timestamp-time()}}");
            var x = setInterval(function() {
                distance--;
                document.getElementById("countdown").innerHTML = distance;
                if (distance <= 0) {
                    clearInterval(x);
                    document.querySelector('.countdown-wrapper').classList.add('d-none');
                    document.querySelector('.try-again-link').classList.remove('d-none');
                }
            }, 1000);
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .cmn-btn.disabled,
        .cmn-btn:disabled {
            color: #fff;
            background-color: #3d2bfb;
            border-color: #3d2bfb;
            opacity: 0.7;
        }
    </style>
@endpush
