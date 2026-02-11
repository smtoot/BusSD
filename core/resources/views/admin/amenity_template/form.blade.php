@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ isset($amenity) ? route('admin.amenity.template.update', $amenity->id) : route('admin.amenity.template.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Amenity Key')</label>
                                    <input type="text" class="form-control" name="key" 
                                           value="{{ old('key', @$amenity->key) }}" 
                                           {{ isset($amenity) && $amenity->is_system ? 'readonly' : 'required' }}>
                                    <small class="text-muted">@lang('Unique identifier (e.g., wifi, ac, usb_charging)')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Display Label')</label>
                                    <input type="text" class="form-control" name="label" 
                                           value="{{ old('label', @$amenity->label) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Icon (Font Awesome)')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fa" id="iconPreview" :class="{{ old('icon', @$amenity->icon ?? 'fa-circle') }}"></i>
                                        </span>
                                        <input type="text" class="form-control" name="icon" id="iconInput"
                                               value="{{ old('icon', @$amenity->icon ?? 'fa-circle') }}" 
                                               placeholder="fa-wifi" required>
                                    </div>
                                    <small class="text-muted">
                                        @lang('Browse icons at') <a href="https://fontawesome.com/v5/search?m=free" target="_blank">FontAwesome</a>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Category')</label>
                                    <select class="form-control select2" name="category" required>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}" {{ old('category', @$amenity->category) == $key ? 'selected' : '' }}>
                                                {{ __($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Sort Order')</label>
                                    <input type="number" class="form-control" name="sort_order" 
                                           value="{{ old('sort_order', @$amenity->sort_order ?? 0) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check form-switch form--switch pl-0 mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               {{ old('is_active', @$amenity->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">@lang('Active')</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.amenity.template.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-arrow-left"></i>@lang('Back')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // Live icon preview
            $('#iconInput').on('input', function() {
                let iconClass = $(this).val();
                $('#iconPreview').attr('class', 'fa ' + iconClass);
            });
        })(jQuery);
    </script>
@endpush
