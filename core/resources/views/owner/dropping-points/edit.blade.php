@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="POST" action="{{ route('owner.dropping-points.update', $droppingPoint->id) }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ $droppingPoint->name }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Type') <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="bus_stand" {{ $droppingPoint->type == 'bus_stand' ? 'selected' : '' }}>@lang('Bus Stand')</option>
                                        <option value="city_center" {{ $droppingPoint->type == 'city_center' ? 'selected' : '' }}>@lang('City Center')</option>
                                        <option value="airport" {{ $droppingPoint->type == 'airport' ? 'selected' : '' }}>@lang('Airport')</option>
                                        <option value="custom" {{ $droppingPoint->type == 'custom' ? 'selected' : '' }}>@lang('Custom')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <input type="checkbox" name="is_active" value="1" {{ $droppingPoint->is_active ? 'checked' : '' }} data-width="100%" data-size="large" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <select name="city_id" class="form-control select2">
                                        <option value="">@lang('Select City')</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $droppingPoint->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Contact Phone')</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ $droppingPoint->contact_phone }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Landmark')</label>
                                    <input type="text" name="landmark" class="form-control" value="{{ $droppingPoint->landmark }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Full Address')</label>
                                    <textarea name="address" class="form-control" rows="2">{{ $droppingPoint->address }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Latitude')</label>
                                    <input type="text" name="latitude" class="form-control" value="{{ $droppingPoint->latitude }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Longitude')</label>
                                    <input type="text" name="longitude" class="form-control" value="{{ $droppingPoint->longitude }}">
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
