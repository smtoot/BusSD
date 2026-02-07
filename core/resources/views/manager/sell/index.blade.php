@extends('manager.layouts.app')
@section('panel')
    @if ($activePackage->count() == 0)
        <div class="alert border border--danger bg--white" role="alert">
            <div class="alert__icon bg--danger"><i class="far fa-bell"></i></div>
            <p class="alert__message">@lang('You\'ve no active package. Please contact with the owner to bye a package')</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @else
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <form method="POST" action="{{ route('manager.sell.search') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Date of Journey')</label>
                                        <input name="date_of_journey" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('From')</label>
                                        <select class="select2 form-control" name="from" required>
                                            <option value="">@lang('Select One')</option>
                                            @foreach ($counters as $counter)
                                                <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('To')</label>
                                        <select class="select2 form-control" name="to" required>
                                            <option value="">@lang('Select One')</option>
                                            @foreach ($counters as $counter)
                                                <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let minYear = parseInt(moment().format('YYYY'), 10);

            $('input[name="date_of_journey"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: minYear,
                maxYear: minYear + 20
            });
        })(jQuery)
    </script>
@endpush
