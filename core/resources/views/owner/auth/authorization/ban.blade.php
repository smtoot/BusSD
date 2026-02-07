@extends('owner.layouts.master')
@php
    $bannedContent = getContent('banned.content', true);
@endphp
@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-8 text-center">
            <img src="{{ frontendImage('banned', @$bannedContent->data_values->image, '700x400') }}"
                alt="@lang('image')" class="mb-4">
            <h4 class="text--danger mb-2">{{ __(@$bannedContent->data_values->heading) }}</h4>
            <p class="mb-4">{{ __($owner->ban_reason) }} </p>
            <a href="{{ route('home') }}" class="btn btn--info btn-sm"> @lang('Go to Home') </a>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
        .container{
            height: 100vh;
            display: flex;
            align-items: center;
        }
    </style>
@endpush
