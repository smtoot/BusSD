@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-4 mb-30">
            <div class="card b-radius--5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 bg--primary align-items-center">
                        <div class="avatar avatar--lg">
                            <img src="{{ getImage(getFilePath('ownerProfile') . '/' . $owner->image, getFileSize('ownerProfile')) }}"
                                alt="Image">
                        </div>
                        <div class="ps-3">
                            <h4 class="text--white">{{ __($owner->fullname) }}</h4>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">{{ __($owner->fullname) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="fw-bold">{{ __($owner->username) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span class="fw-bold">{{ $owner->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span class="fw-bold">{{ $owner->mobile }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Country')
                            <span class="fw-bold">{{ $owner->country_name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Profile Information')</h5>
                    <form action="{{ route('owner.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xxl-4 col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ $owner->image }}" class="w-100" type="ownerProfile"
                                        :required=false />
                                </div>
                            </div>
                            <div class="col-xxl-8 col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group ">
                                            <label>@lang('First Name')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $owner->firstname }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group ">
                                            <label>@lang('Last Name')</label>
                                            <input class="form-control" type="text" name="lastname"
                                                value="{{ $owner->lastname }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address"
                                        value="{{ $owner->address }}">
                                </div>
                                <div class="form-group">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{ $owner->state }}">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Zip Code')</label>
                                    <input class="form-control" type="text" name="zip" value="{{ $owner->zip }}">
                                </div>
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{ $owner->city }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.password') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-key"></i>@lang('Password Setting')
    </a>
@endpush

@push('style')
    <style>
        .list-group-item:first-child {
            border-top-left-radius: unset;
            border-top-right-radius: unset;
        }
    </style>
@endpush
