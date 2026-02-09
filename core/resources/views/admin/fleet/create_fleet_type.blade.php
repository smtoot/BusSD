@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Create New Fleet Type')</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.fleet.fleet_types.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Seat Layout') <span class="text-danger">*</span></label>
                                    <select name="seat_layout" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($seatLayouts as $item)
                                            <option value="{{ $item->id }}">{{ __($item->layout) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Number of Deck') <span class="text-danger">*</span></label>
                                    <input type="number" name="deck" class="form-control" placeholder="@lang('Example: 1 / 2')" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Seats per Deck') <span class="text-danger">*</span></label>
                                    <div class="seat-number-wrapper"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Has AC') <span class="text-danger">*</span></label>
                                    <select name="has_ac" class="form-control select2" required>
                                        <option value="{{ \App\Constants\Status::YES }}">@lang('Yes')</option>
                                        <option value="{{ \App\Constants\Status::NO }}">@lang('No')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                            <a href="{{ route('admin.fleet.fleet_types') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
