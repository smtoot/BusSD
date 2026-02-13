@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ isset($template) ? route('owner.route.builder.update', $template->id) : route('owner.route.builder.store') }}" method="POST">
                @csrf
                <div class="card b-radius--10 mb-4">
                    <div class="card-header bg--primary">
                        <h5 class="text-white">@lang('Template Basic Info')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Template Name') <span class="text--danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', @$template->name) }}" placeholder="@lang('e.g., Express Route A')" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Base Route (Optional)')</label>
                                    <select name="base_route_id" class="form-control select2-basic">
                                        <option value="">@lang('Select Route')</option>
                                        @foreach($routes as $route)
                                            <option value="{{ $route->id }}" {{ old('base_route_id', @$template->base_route_id) == $route->id ? 'selected' : '' }}>{{ $route->title ?: $route->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea name="description" class="form-control" rows="2">{{ old('description', @$template->description) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" {{ old('is_active', @$template->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">@lang('Active Status')</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card b-radius--10">
                    <div class="card-header bg--dark d-flex justify-content-between align-items-center">
                        <h5 class="text-white">@lang('Route Stops / Timeline')</h5>
                        <button type="button" class="btn btn-sm btn--success addStopBtn"><i class="la la-plus"></i> @lang('Add Stop')</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table--light style--two mb-0" id="stopsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">@lang('Seq')</th>
                                        <th>@lang('City') <span class="text--danger">*</span></th>
                                        <th>@lang('Time Offset') <small>(min)</small></th>
                                        <th>@lang('Dwell') <small>(min)</small></th>
                                        <th>@lang('Dist') <small>(km)</small></th>
                                        <th>@lang('B/D')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody class="sortable-stops">
                                    @php
                                        $stops = old('stops', isset($template) ? $template->stops->toArray() : []);
                                    @endphp
                                    
                                    @foreach($stops as $index => $stop)
                                        <tr class="stop-row">
                                            <td class="text-center handle">
                                                <span class="seq-num">{{ $index + 1 }}</span>
                                                <i class="la la-arrows-v text-muted" style="cursor: move"></i>
                                            </td>
                                            <td>
                                                <select name="stops[{{ $index }}][city_id]" class="form-control form-control-sm select2-basic" required>
                                                    <option value="">@lang('Select City')</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}" {{ @$stop['city_id'] == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="stops[{{ $index }}][time_offset_minutes]" class="form-control form-control-sm" value="{{ @$stop['time_offset_minutes'] }}" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" name="stops[{{ $index }}][dwell_time_minutes]" class="form-control form-control-sm" value="{{ @$stop['dwell_time_minutes'] ?? 5 }}" min="0">
                                            </td>
                                            <td>
                                                <input type="number" step="0.1" name="stops[{{ $index }}][distance_from_previous]" class="form-control form-control-sm" value="{{ @$stop['distance_from_previous'] ?? 0 }}" min="0">
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column text-left" style="font-size: 0.8rem">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="stops[{{ $index }}][boarding_allowed]" class="custom-control-input" id="b_{{ $index }}" value="1" {{ @$stop['boarding_allowed'] ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="b_{{ $index }}">@lang('Board')</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mt-1">
                                                        <input type="checkbox" name="stops[{{ $index }}][dropping_allowed]" class="custom-control-input" id="d_{{ $index }}" value="1" {{ @$stop['dropping_allowed'] ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="d_{{ $index }}">@lang('Drop')</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn--danger removeStopBtn"><i class="la la-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Save Route Template')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden Template Row for JS --}}
    <table class="d-none">
        <tr id="stopRowTemplate" class="stop-row">
            <td class="text-center handle">
                <span class="seq-num">#</span>
                <i class="la la-arrows-v text-muted" style="cursor: move"></i>
            </td>
            <td>
                <select name="stops[XX][city_id]" class="form-control form-control-sm city-select" required>
                    <option value="">@lang('Select City')</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="stops[XX][time_offset_minutes]" class="form-control form-control-sm" value="0" min="0" required>
            </td>
            <td>
                <input type="number" name="stops[XX][dwell_time_minutes]" class="form-control form-control-sm" value="5" min="0">
            </td>
            <td>
                <input type="number" step="0.1" name="stops[XX][distance_from_previous]" class="form-control form-control-sm" value="0" min="0">
            </td>
            <td>
                <div class="d-flex flex-column text-left" style="font-size: 0.8rem">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="stops[XX][boarding_allowed]" class="custom-control-input b-check" id="b_XX" value="1" checked>
                        <label class="custom-control-label b-label" for="b_XX">@lang('Board')</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-1">
                        <input type="checkbox" name="stops[XX][dropping_allowed]" class="custom-control-input d-check" id="d_XX" value="1" checked>
                        <label class="custom-control-label d-label" for="d_XX">@lang('Drop')</label>
                    </div>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn--danger removeStopBtn"><i class="la la-trash"></i></button>
            </td>
        </tr>
    </table>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.route.builder.index') }}" class="btn btn-sm btn--dark box--shadow1 text--small">
        <i class="la la-arrow-left"></i> @lang('Back to List')
    </a>
@endpush

@push('style')
    <style>
        .handle { width: 50px; }
        .seq-num { display: block; font-weight: bold; margin-bottom: 2px; }
    </style>
@endpush

@push('script-lib')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";

            var stopIndex = {{ count($stops) }};

            function updateSequence() {
                $('.sortable-stops tr').each(function(index) {
                    $(this).find('.seq-num').text(index + 1);
                });
            }

            $('.addStopBtn').on('click', function() {
                var html = $('#stopRowTemplate').clone();
                html.removeAttr('id');
                
                var stopHtml = html.html().replace(/XX/g, stopIndex);
                html.html(stopHtml);
                
                $('.sortable-stops').append(html);
                html.find('.city-select').select2();
                
                stopIndex++;
                updateSequence();
            });

            $(document).on('click', '.removeStopBtn', function() {
                $(this).closest('tr').remove();
                updateSequence();
            });

            // Initialize select2
            $('.select2-basic').select2();

            // Handle sorting if jQuery UI is available
            if ($.isFunction($.fn.sortable)) {
                $(".sortable-stops").sortable({
                    handle: ".handle",
                    update: function(event, ui) {
                        updateSequence();
                    }
                });
            }
            
            @if(count($stops) == 0)
                $('.addStopBtn').click(); // Add first row automatically
                $('.addStopBtn').click(); // Add second row automatically (min 2)
            @endif

        })(jQuery);
    </script>
@endpush
