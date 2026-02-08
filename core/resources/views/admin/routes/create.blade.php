@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Create New Route')</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.routes.create') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Route Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Starting Point') <span class="text-danger">*</span></label>
                                    <select name="starting_point" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($counters as $counter)
                                            <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Destination Point') <span class="text-danger">*</span></label>
                                    <select name="destination_point" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($counters as $counter)
                                            <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Distance (km)')</label>
                                    <input type="text" name="distance" class="form-control" placeholder="@lang('Example: 150')" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Time (hours)')</label>
                                    <input type="text" name="time" class="form-control" placeholder="@lang('Example: 5')" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Stoppages')</label>
                                    <select name="stoppages[]" class="form-control select2" multiple>
                                        @foreach ($counters as $counter)
                                            <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">@lang('Hold Ctrl/Cmd to select multiple')</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                            <a href="{{ route('admin.routes.index') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
