@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{ route('owner.boarding-points.update', $boardingPoint->id) }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ $boardingPoint->name }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Type') <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="bus_stand" {{ $boardingPoint->type == 'bus_stand' ? 'selected' : '' }}>@lang('Bus Stand')</option>
                                        <option value="highway_pickup" {{ $boardingPoint->type == 'highway_pickup' ? 'selected' : '' }}>@lang('Highway Pickup')</option>
                                        <option value="city_center" {{ $boardingPoint->type == 'city_center' ? 'selected' : '' }}>@lang('City Center')</option>
                                        <option value="airport" {{ $boardingPoint->type == 'airport' ? 'selected' : '' }}>@lang('Airport')</option>
                                        <option value="custom" {{ $boardingPoint->type == 'custom' ? 'selected' : '' }}>@lang('Custom')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <input type="checkbox" name="is_active" value="1" {{ $boardingPoint->is_active ? 'checked' : '' }} data-width="100%" data-size="large" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <select name="city_id" class="form-control select2">
                                        <option value="">@lang('Select City')</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $boardingPoint->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
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
                                            <option value="{{ $counter->id }}" {{ $boardingPoint->counter_id == $counter->id ? 'selected' : '' }}>{{ $counter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Contact Phone')</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ $boardingPoint->contact_phone }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Landmark')</label>
                                    <input type="text" name="landmark" class="form-control" value="{{ $boardingPoint->landmark }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Full Address')</label>
                                    <textarea name="address" class="form-control" rows="2">{{ $boardingPoint->address }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Latitude')</label>
                                    <input type="text" name="latitude" class="form-control" value="{{ $boardingPoint->latitude }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Longitude')</label>
                                    <input type="text" name="longitude" class="form-control" value="{{ $boardingPoint->longitude }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
