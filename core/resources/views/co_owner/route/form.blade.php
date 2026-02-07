@extends('co_owner.layouts.app')
@section('panel')
    <div class="card">
        <form action="{{ route('co-owner.trip.route.store', @$route->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" value="{{ old('name', @$route->name) }}" class="form-control"
                                required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Starting Point')</label>
                            <select name="starting_point" class="form-control select2" data-minimum-results-for-search="-1"
                                required>
                                <option selected>@lang('Select One')</option>
                                @foreach ($counters as $counter)
                                    <option value="{{ $counter->id }}" @selected(old('starting_point', @$route->starting_point) == $counter->id)>
                                        {{ __($counter->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Destination Point')</label>
                            <select name="destination_point" class="form-control select2"
                                data-minimum-results-for-search="-1" required>
                                <option selected>@lang('Select One')</option>
                                @foreach ($counters as $counter)
                                    <option value="{{ $counter->id }}" @selected(old('destination_point', @$route->destination_point) == $counter->id)>
                                        {{ __($counter->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 form-group">
                        <button type="button" class="btn btn--primary addStoppageBtn">
                            <i class="la la-plus"></i> @lang('Next Stoppage')
                        </button>
                        <small class="text--danger d-block mt-2">
                            <i class="las la-info-circle"></i>
                            <i>@lang('Make sure that you are adding stoppages serially followed by the starting point')</i>
                        </small>
                    </div>
                    <div class="col-12">
                        <div class="row stoppages-wrapper">
                            @foreach ($stoppages as $item)
                                <div class="col-lg-3 col-md-4 parentStoppage">
                                    <div class="input-group mb-2">
                                        <select class="form-control select2" data-minimum-results-for-search="-1"
                                            name="stoppages[]">
                                            <option value="" selected>@lang('Select One')</option>
                                            @foreach ($counters as $stoppage)
                                                <option value="{{ $stoppage->id }}" @selected($item->id == $stoppage->id)>
                                                    {{ $stoppage->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button"
                                            class="input-group-text bg-danger border--danger removeStoppage">
                                            <i class="la la-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Distance from Staring to Destination')</label>
                            <input type="text" name="distance" value="{{ old('distance', @$route->distance) }}"
                                class="form-control" placeholder="@lang('50 Miles')" />
                            <small class="text-danger">
                                <i class="las la-info-circle"></i><i>@lang('Keep SPACE between value and unit')</i>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Time (Approximate)')</label>
                            <input type="text" name="time" value="{{ old('time', @$route->time) }}"
                                class="form-control" placeholder="@lang('3 Hour')" />
                            <small class="text-danger">
                                <i class="las la-info-circle"></i><i>@lang('Keep SPACE between value and unit')</i>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary w-100 h-45">
                    @lang('Submit')
                </button>
            </div>
        </form>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('co-owner.trip.route.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let stoppagesWrapper = $('.stoppages-wrapper');
            $('.addStoppageBtn').on('click', function() {
                stoppagesWrapper.append(`
                    <div class="col-lg-3 col-md-4 parentStoppage">
                        <div class="input-group mb-2">
                            <select class="select2 form-control" data-minimum-results-for-search="-1"  name="stoppages[]">
                                <option value="" selected>@lang('Select One')</option>
                                @foreach ($counters as $stoppage)
                                    <option value="{{ $stoppage->id }}">{{ $stoppage->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="input-group-text bg-danger border--danger removeStoppage">
                                <i class="la la-times"></i>
                            </button>
                        </div>
                    </div>`);
                $('.select2').select2();
            });

            $(document).on('click', '.removeStoppage', function() {
                $(this).closest('.parentStoppage').remove();
            });
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .input-group {
            flex-wrap: unset;
        }

        .input-group>.position-relative {
            width: 100% !important;
        }

        .select2-container {
            width: auto !important;
        }

        .select2-container:has(.select2-selection--single) {
            width: 100% !important;
        }
    </style>
@endpush
