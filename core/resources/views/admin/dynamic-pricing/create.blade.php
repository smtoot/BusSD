@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Add New Dynamic Pricing Rule') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dynamic-pricing.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.dynamic-pricing.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Owner') }}</label>
                                    <select name="owner_id" class="form-control select2">
                                        <option value="0">{{ __('All Owners (Global)') }}</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Name') }} *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Name (Arabic)') }}</label>
                                    <input type="text" name="name_ar" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Rule Type') }} *</label>
                                    <select name="rule_type" class="form-control" required>
                                        <option value="surge">{{ __('Surge Pricing') }}</option>
                                        <option value="early_bird">{{ __('Early Bird Discount') }}</option>
                                        <option value="last_minute">{{ __('Last Minute Surge') }}</option>
                                        <option value="weekend">{{ __('Weekend Pricing') }}</option>
                                        <option value="holiday">{{ __('Holiday Pricing') }}</option>
                                        <option value="custom">{{ __('Custom') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Operator Type') }} *</label>
                                    <select name="operator_type" class="form-control" required>
                                        <option value="percentage">{{ __('Percentage') }}</option>
                                        <option value="fixed">{{ __('Fixed Amount') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Value') }} *</label>
                                    <input type="number" name="value" class="form-control" step="0.01" required>
                                    <small class="text-muted">{{ __('Use positive for surges, negative for discounts') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Valid From') }} *</label>
                                    <input type="date" name="valid_from" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Valid Until') }} *</label>
                                    <input type="date" name="valid_until" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Applicable Days') }}</label>
                                    <div class="d-flex flex-wrap">
                                        @foreach([0 => __('Sunday'), 1 => __('Monday'), 2 => __('Tuesday'), 3 => __('Wednesday'), 4 => __('Thursday'), 5 => __('Friday'), 6 => __('Saturday')] as $day => $label)
                                            <div class="form-check mr-3">
                                                <input type="checkbox" name="applicable_days[]" value="{{ $day }}" class="form-check-input" id="day_{{ $day }}">
                                                <label class="form-check-label" for="day_{{ $day }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Start Time') }}</label>
                                    <input type="time" name="start_time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('End Time') }}</label>
                                    <input type="time" name="end_time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Min Hours Before Departure') }}</label>
                                    <input type="number" name="min_hours_before_departure" class="form-control" min="0">
                                    <small class="text-muted">{{ __('For early bird rules') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Max Hours Before Departure') }}</label>
                                    <input type="number" name="max_hours_before_departure" class="form-control" min="0">
                                    <small class="text-muted">{{ __('For last minute rules') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Min Seats Available') }}</label>
                                    <input type="number" name="min_seats_available" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Max Seats Available') }}</label>
                                    <input type="number" name="max_seats_available" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Priority') }} *</label>
                                    <input type="number" name="priority" class="form-control" min="0" max="100" value="50" required>
                                    <small class="text-muted">{{ __('Higher priority rules are applied first (0-100)') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('admin.dynamic-pricing.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
