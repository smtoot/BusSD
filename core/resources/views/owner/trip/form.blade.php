@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('owner.trip.store', @$trip->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div id="overlay">
                            <div class="cv-spinner">
                                <span class="spinner"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Title')</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', @$trip->title) }}" readonly required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Fleet Type')</label>
                                    <select class="select2 form-control" name="fleet_type" required>
                                        <option selected value="">@lang('Select One')</option>
                                        @foreach ($fleetTypes as $fleetType)
                                            <option value="{{ $fleetType->id }}" data-name="{{ $fleetType->name }}"
                                                @selected(old('fleet_type', @$trip->fleet_type_id) == $fleetType->id)>
                                                {{ $fleetType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Route')</label>
                                    <select class="select2 form-control" name="route" required>
                                        <option selected value="">@lang('Select One')</option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}" data-name="{{ $route->name }}"
                                                data-source_id="{{ $route->starting_point }}"
                                                data-source="{{ $route->startingPoint->name }}"
                                                data-destination_id="{{ $route->destination_point }}"
                                                data-destination="{{ $route->destinationPoint->name }}"
                                                @selected(old('route', @$trip->route_id) == $route->id)>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="from-to-wrapper col-12">
                                @if ($trip)
                                    <div class="form-group">
                                        <label>@lang('From')</label>
                                        <select class="form-control from_to" name="from" required>
                                            <option value="{{ $trip->starting_point }}"
                                                data-name="{{ $trip->startingPoint->name }}" selected>
                                                {{ $trip->startingPoint->name }}
                                            </option>
                                            <option value="{{ $trip->destination_point }}"
                                                data-name="{{ $trip->destinationPoint->name }}">
                                                {{ $trip->destinationPoint->name }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('To')</label>
                                        <span class="value-change-button bg-primary mb-2">
                                            <i class="las la-exchange-alt"></i>
                                        </span>
                                        <select class="form-control from_to" name="to" required>
                                            <option value="{{ $trip->starting_point }}"
                                                data-name="{{ $trip->startingPoint->name }}">
                                                {{ $trip->startingPoint->name }}
                                            </option>
                                            <option value="{{ $trip->destination_point }}"
                                                data-name="{{ $trip->destinationPoint->name }}" selected>
                                                {{ $trip->destinationPoint->name }}</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Schedule')</label>
                                    <select class="select2 form-control" name="schedule" required>
                                        <option selected value="">@lang('Select One')</option>
                                        @foreach ($schedules as $schedule)
                                            <option value="{{ $schedule->id }}"
                                                data-name="{{ showDateTime($schedule->starts_from, 'H:i a') }} - {{ showDateTime($schedule->ends_at, 'H:i a') }}"
                                                @selected(old('schedule', @$trip->schedule_id) == $schedule->id)>
                                                {{ showDateTime($schedule->starts_from, 'H:i a') }} -
                                                {{ showDateTime($schedule->ends_at, 'H:i a') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Day Off')</label>
                                    <select class="form-control select2-auto-tokenize" multiple="multiple" name="day_off[]"
                                        required>
                                        <option value="0">@lang('Sunday')</option>
                                        <option value="1">@lang('Monday')</option>
                                        <option value="2">@lang('Tuesday')</option>
                                        <option value="3">@lang('Wednesday')</option>
                                        <option value="4">@lang('Thursday')</option>
                                        <option value="5">@lang('Friday')</option>
                                        <option value="6">@lang('Saturday')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('B2C Locked Seats (Counter Only)')</label>
                                    <select class="form-control select2-auto-tokenize" multiple="multiple" name="b2c_locked_seats[]">
                                        @if(@$trip->b2c_locked_seats)
                                            @foreach($trip->b2c_locked_seats as $seat)
                                                <option value="{{ $seat }}" selected>{{ $seat }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="text-muted">@lang('Enter seat numbers you want to keep for counter-only sales (e.g. 1, 2, A1, A2). Press enter after each seat.')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('owner.trip.index') }}" />
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {
            @if ($trip)
                var day_off = JSON.parse('@php echo json_encode($trip->day_off) @endphp');
                $('select[name="day_off[]"]').val(day_off).change();
            @endif

            $('.from_to').select2();

            $('select[name="route"]').on('change', function() {
                var source = $('select[name="route"]').find("option:selected").data('source');
                var source_id = $('select[name="route"]').find("option:selected").data('source_id');
                var destination = $('select[name="route"]').find("option:selected").data('destination');
                var destination_id = $('select[name="route"]').find("option:selected").data('destination_id');

                var contents = `
                            <div class="form-group">
                                <label>@lang('From')</label>
                                <select class="form-control from_to" name="from" required>
                                    <option value="${source_id}" data-name="${source}" selected>${source}</option>
                                    <option value="${destination_id}" data-name="${destination}">${destination}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('To')</label>
                                <span class="value-change-button bg-primary mb-1">
                                    <i class="las la-exchange-alt"></i>
                                </span>
                                <select class="form-control from_to" name="to" required>
                                    <option value="${source_id}" data-name="${source}">${source}</option>
                                    <option value="${destination_id}" data-name="${destination}" selected>${destination}</option>
                                </select>
                            </div>
                            `;
                $('.from-to-wrapper').fadeIn("slow").html(contents);
                $('.from_to').select2();
            });

            $('select[name="fleet_type"]').on('change', function() {
                makeTitle();
            });

            $('select[name="schedule"]').on('change', function() {
                makeTitle();
            });

            $('select[name="route"]').on('change', function() {
                makeTitle();
            });

            $('select[name="from"]').on('change', function() {
                makeTitle();
            });

            $('select[name="to"]').on('change', function() {
                makeTitle();
            });

            $('.value-change-button').on('click', function(e) {
                var from = $('select[name="from"]').val();
                var to = $('select[name="to"]').val();

                $('select[name="from"]').val(to).change();
                $('select[name="to"]').val(from).change();
                makeTitle();
            });

            function makeTitle() {
                var data1 = $('select[name="fleet_type"]').find("option:selected").data('name');
                var data2 = $('select[name="route"]').find("option:selected").data('name');
                var data4 = $('select[name="from"]').find("option:selected").data('name');
                var data5 = $('select[name="to"]').find("option:selected").data('name');
                var data3 = $('select[name="schedule"]').find("option:selected").data('name');
                var data = [];
                var title = '';

                if (data1 != undefined) data.push(data1);

                if (data2 != undefined) data.push(data2);

                if (data3 != undefined) data.push(data3);

                if (data4 != undefined) data.push(data4);

                if (data5 != undefined) data.push(data5);

                if (data1 != undefined && data2 != undefined) {
                    $("#overlay").fadeIn(300);
                    $.ajax({
                        type: "get",
                        url: "{{ route('owner.trip.ticket.check_price') }}",
                        data: {
                            "fleet_type_id": $('.card-body').find('select[name="fleet_type"]').val(),
                            "route_id": $('.card-body').find('select[name="route"]').val()
                        },
                        success: function(response) {
                            if (response.error) {
                                notify('error', response.error);
                            }
                        }
                    }).done(function() {
                        setTimeout(function() {
                            $("#overlay").fadeOut(300);
                        }, 500);
                    });
                }

                $.each(data, function(index, value) {
                    if (index > 0) {
                        title += index > 3 ? ' to ' : ' - ';
                    }
                    title += value;
                });
                $('input[name="title"]').val(title);
            }
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .input-group {
            flex-wrap: unset;
        }

        .select2-container {
            width: auto !important;
        }

        .select2-container:has(.select2-selection--single, .select2-selection--multiple) {
            width: 100% !important;
        }
    </style>
@endpush
