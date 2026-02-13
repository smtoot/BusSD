@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{ route('admin.boarding-points.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Type') <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="bus_stand">@lang('Bus Stand')</option>
                                        <option value="highway_pickup">@lang('Highway Pickup')</option>
                                        <option value="city_center">@lang('City Center')</option>
                                        <option value="airport">@lang('Airport')</option>
                                        <option value="custom">@lang('Custom')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <input type="checkbox" name="is_active" value="1" checked data-width="100%" data-size="large" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <select name="city_id" class="form-control select2">
                                        <option value="">@lang('Select City')</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Counter')</label>
                                    <select name="counter_id" class="form-control select2">
                                        <option value="">@lang('Select Counter')</option>
                                        @foreach($counters as $counter)
                                            <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Contact Phone')</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Landmark')</label>
                                    <input type="text" name="landmark" class="form-control" value="{{ old('landmark') }}" placeholder="@lang('e.g. Near City Mall')">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Full Address')</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Latitude')</label>
                                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="@lang('e.g. 15.5007')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Longitude')</label>
                                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="@lang('e.g. 32.5599')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
