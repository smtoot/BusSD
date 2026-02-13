@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Edit Dropping Point') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dropping-points.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.dropping-points.update', $droppingPoint->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Owner') }}</label>
                                    <select name="owner_id" class="form-control select2">
                                        <option value="0" {{ $droppingPoint->owner_id == 0 ? 'selected' : '' }}>{{ __('All Owners (Global)') }}</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}" {{ $droppingPoint->owner_id == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('City') }} *</label>
                                    <select name="city_id" class="form-control select2" required>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $droppingPoint->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $droppingPoint->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Name (Arabic)') }}</label>
                                    <input type="text" name="name_ar" class="form-control" value="{{ $droppingPoint->name_ar ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Address') }} *</label>
                                    <input type="text" name="address" class="form-control" value="{{ $droppingPoint->address }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Address (Arabic)') }}</label>
                                    <input type="text" name="address_ar" class="form-control" value="{{ $droppingPoint->address_ar ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Latitude') }} *</label>
                                    <input type="number" name="latitude" class="form-control" step="any" value="{{ $droppingPoint->latitude }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Longitude') }} *</label>
                                    <input type="number" name="longitude" class="form-control" step="any" value="{{ $droppingPoint->longitude }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Landmark') }}</label>
                                    <input type="text" name="landmark" class="form-control" value="{{ $droppingPoint->landmark ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Landmark (Arabic)') }}</label>
                                    <input type="text" name="landmark_ar" class="form-control" value="{{ $droppingPoint->landmark_ar ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Phone') }}</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $droppingPoint->phone ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Email') }}</label>
                                    <input type="email" name="email" class="form-control" value="{{ $droppingPoint->email ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Type') }} *</label>
                                    <select name="type" class="form-control" required>
                                        <option value="bus_stand" {{ $droppingPoint->type == 'bus_stand' ? 'selected' : '' }}>{{ __('Bus Stand') }}</option>
                                        <option value="city_center" {{ $droppingPoint->type == 'city_center' ? 'selected' : '' }}>{{ __('City Center') }}</option>
                                        <option value="airport" {{ $droppingPoint->type == 'airport' ? 'selected' : '' }}>{{ __('Airport') }}</option>
                                        <option value="custom" {{ $droppingPoint->type == 'custom' ? 'selected' : '' }}>{{ __('Custom') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Sort Order') }}</label>
                                    <input type="number" name="sort_order" class="form-control" min="0" value="{{ $droppingPoint->sort_order ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $droppingPoint->status ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="0" {{ !$droppingPoint->status ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('admin.dropping-points.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
