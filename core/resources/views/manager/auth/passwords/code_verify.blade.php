@extends('manager.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
        <div class="container custom-container d-flex justify-content-center">
            <div class="login-area">
                <div class="text-center mb-3">
                    <h2 class="text-white mb-2">@lang('Verify Code')</h2>
                </div>
                <form action="{{ route('manager.password.verify.code') }}" method="POST" class="login-form w-100">
                    @csrf
                    <p class="mb-2 text-white">@lang('A 6 digit verification code sent to your email address') :  {{ showEmailAddress($email) }}</p>
                    <input type="hidden" name="email" value="{{ $email }}">

                    @include('manager.partials.verification_code')

                    <button type="submit" class="btn cmn-btn w-100">@lang('Submit')</button>
                    <div class="form-group mt-3 text-white">
                        @lang('Please check including your Junk/Spam Folder. if not found, you can')
                        <a href="{{ route('manager.password.request') }}" class="text-secondary">@lang('Try to send again')</a>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('manager.login') }}" class="text-white"><i class="las la-sign-in-alt"></i> @lang('Back to Login')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/verification_code.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('[name=code]').on('input', function() {
                $(this).val(function(i, val) {
                    if (val.length == 6) {
                        $('form').find('button[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                        $('form').find('button[type=submit]').removeClass('disabled');
                        $('form')[0].submit();
                    } else {
                        $('form').find('button[type=submit]').addClass('disabled');
                    }
                    if (val.length > 6) {
                        return val.substring(0, val.length - 1);
                    }
                    return val;
                });

                for (let index = $(this).val().length; index >= 0; index--) {
                    $($('.boxes span')[index]).html('');
                }
            });

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
