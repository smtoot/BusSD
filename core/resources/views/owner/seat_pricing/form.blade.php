@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="{{ route('owner.seat.pricing.store', @$modifier->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text--danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', @$modifier->name) }}" placeholder="@lang('e.g., Premium Front Rows')" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Priority') <small>(@lang('0-100, higher applies last'))</small></label>
                                    <input type="number" name="priority" class="form-control" value="{{ old('priority', @$modifier->priority ?: 0) }}" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea name="description" class="form-control" rows="2">{{ old('description', @$modifier->description) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Modifier Type') <span class="text--danger">*</span></label>
                                    <select name="modifier_type" class="form-control" required>
                                        <option value="percentage" {{ old('modifier_type', @$modifier->modifier_type) == 'percentage' ? 'selected' : '' }}>@lang('Percentage (%)')</option>
                                        <option value="fixed" {{ old('modifier_type', @$modifier->modifier_type) == 'fixed' ? 'selected' : '' }}>@lang('Fixed Amount') ({{ gs('cur_text') }})</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Value') <span class="text--danger">*</span> <small>(@lang('Positive to increase, Negative to discount'))</small></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="modifier_value" class="form-control" value="{{ old('modifier_value', @$modifier->modifier_value) }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text modifier-unit">{{ old('modifier_type', @$modifier->modifier_type) == 'fixed' ? gs('cur_text') : '%' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Applies To') <span class="text--danger">*</span></label>
                                    <select name="applies_to" id="applies_to" class="form-control" required>
                                        <option value="all" {{ old('applies_to', @$modifier->applies_to) == 'all' ? 'selected' : '' }}>@lang('All Seats')</option>
                                        <option value="position" {{ old('applies_to', @$modifier->applies_to) == 'position' ? 'selected' : '' }}>@lang('By Position/Row')</option>
                                        <option value="specific_seats" {{ old('applies_to', @$modifier->applies_to) == 'specific_seats' ? 'selected' : '' }}>@lang('Specific Seat Numbers')</option>
                                        <option value="category" {{ old('applies_to', @$modifier->applies_to) == 'category' ? 'selected' : '' }}>@lang('By Category')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Trip (Optional)') <small>(@lang('Leave empty for all trips'))</small></label>
                                    <select name="trip_id" class="form-control select2-basic">
                                        <option value="">@lang('All Trips')</option>
                                        @foreach($trips as $trip)
                                            <option value="{{ $trip->id }}" {{ old('trip_id', @$modifier->trip_id) == $trip->id ? 'selected' : '' }}>{{ $trip->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Fleet Type (Optional)') <small>(@lang('Leave empty for all fleets'))</small></label>
                                    <select name="fleet_type_id" class="form-control select2-basic">
                                        <option value="">@lang('All Fleets')</option>
                                        @foreach($fleetTypes as $fleet)
                                            <option value="{{ $fleet->id }}" {{ old('fleet_type_id', @$modifier->fleet_type_id) == $fleet->id ? 'selected' : '' }}>{{ $fleet->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Position Fields --}}
                            <div class="col-md-12 position-fields {{ old('applies_to', @$modifier->applies_to) == 'position' ? '' : 'd-none' }}">
                                <div class="row border p-3 mb-3 mx-1 b-radius--5">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Row Range Start (optional)')</label>
                                            <input type="number" name="row_range_start" class="form-control" value="{{ old('row_range_start', @$modifier->row_range_start) }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Row Range End (optional)')</label>
                                            <input type="number" name="row_range_end" class="form-control" value="{{ old('row_range_end', @$modifier->row_range_end) }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Seat Type (optional)')</label>
                                            <select name="seat_type" class="form-control">
                                                <option value="">@lang('Any Type')</option>
                                                <option value="window" {{ old('seat_type', @$modifier->seat_type) == 'window' ? 'selected' : '' }}>@lang('Window')</option>
                                                <option value="aisle" {{ old('seat_type', @$modifier->seat_type) == 'aisle' ? 'selected' : '' }}>@lang('Aisle')</option>
                                                <option value="middle" {{ old('seat_type', @$modifier->seat_type) == 'middle' ? 'selected' : '' }}>@lang('Middle')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Specific Seats Field --}}
                            <div class="col-md-12 specific-fields {{ old('applies_to', @$modifier->applies_to) == 'specific_seats' ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label>@lang('Seat Numbers (comma separated)') <span class="text--danger">*</span></label>
                                    <textarea name="seat_positions_text" class="form-control" placeholder="@lang('e.g., 1A, 1B, 2A, 2B')">{{ old('seat_positions_text', @$modifier->seat_positions ? implode(', ', $modifier->seat_positions) : '') }}</textarea>
                                </div>
                            </div>
                            
                            {{-- Category Field --}}
                            <div class="col-md-12 category-fields {{ old('applies_to', @$modifier->applies_to) == 'category' ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label>@lang('Seat Category') <span class="text--danger">*</span></label>
                                    <input type="text" name="seat_category" class="form-control" value="{{ old('seat_category', @$modifier->seat_category) }}" placeholder="@lang('e.g., VIP, Executive')">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" {{ old('is_active', @$modifier->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">@lang('Active Status')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn--primary btn-block">@lang('Save Modifier')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.seat.pricing.index') }}" class="btn btn-sm btn--dark box--shadow1 text--small">
        <i class="la la-arrow-left"></i> @lang('Back to List')
    </a>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            
            $('select[name="modifier_type"]').on('change', function() {
                var unit = $(this).val() == 'fixed' ? '{{ gs('cur_text') }}' : '%';
                $('.modifier-unit').text(unit);
            });

            $('#applies_to').on('change', function() {
                var val = $(this).val();
                $('.position-fields, .specific-fields, .category-fields').addClass('d-none');
                
                if(val == 'position') {
                    $('.position-fields').removeClass('d-none');
                } else if(val == 'specific_seats') {
                    $('.specific-fields').removeClass('d-none');
                } else if(val == 'category') {
                    $('.category-fields').removeClass('d-none');
                }
            });

            // Initialize select2 if available
            if ($('.select2-basic').length) {
                $('.select2-basic').select2();
            }
        })(jQuery);
    </script>
@endpush
