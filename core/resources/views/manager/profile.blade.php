@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-4 mb-30">
            <div class="card b-radius--5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 bg--primary align-items-center">
                        <div class="avatar avatar--lg">
                            <img src="{{ getImage(getFilePath('counter_manager') . '/' . $manager->image, getFileSize('counter_manager')) }}"
                                alt="Image">
                        </div>
                        <div class="ps-3">
                            <h4 class="text--white">{{ __($manager->fullname) }}</h4>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">{{ __($manager->fullname) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="fw-bold">{{ __($manager->username) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span class="fw-bold">{{ $manager->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span class="fw-bold">{{ $manager->mobile }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Country')
                            <span class="fw-bold">{{ $manager->country_name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Profile Information')</h5>
                    <form action="{{ route('manager.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xxl-4 col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ $manager->image }}" class="w-100" type="counter_manager"
                                        :required=false />
                                </div>
                            </div>
                            <div class="col-xxl-8 col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group ">
                                            <label>@lang('First Name')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $manager->firstname }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group ">
                                            <label>@lang('Last Name')</label>
                                            <input class="form-control" type="text" name="lastname"
                                                value="{{ $manager->lastname }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address"
                                        value="{{ $manager->address }}">
                                </div>
                                <div class="form-group">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state"
                                        value="{{ $manager->state }}">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Zip Code')</label>
                                    <input class="form-control" type="text" name="zip" value="{{ $manager->zip }}">
                                </div>
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{ $manager->city }}">
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
    <a href="{{ route('manager.password') }}" class="btn btn-sm btn-outline--primary">
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
