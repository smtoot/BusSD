@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('owner.trip.ticket.price.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div id="overlay">
                            <div class="cv-spinner">
                                <span class="spinner"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Fleet Type')</label>
                            <select class="select2 form-control" name="fleet_type" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($fleetTypes as $fleetType)
                                    <option value="{{ $fleetType->id }}">
                                        {{ $fleetType->name }} - {{ $fleetType->has_ac ? trans('Ac') : trans('Non Ac') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Route')</label>
                            <select class="select2 form-control" name="route" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="price-error-message"></div>

                        <div class="form-group">
                            <label>@lang('Price for Source to Destination')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ @$owner->general_settings->cur_sym }}</span>
                                <input type="text" class="form-control numeric-validation main_price" name="main_price"
                                    required />
                            </div>
                            <div class="price-wrapper"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 submit-button">@lang('Set Ticket Price')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('owner.trip.ticket.price.index') }}" />
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {
            let routeId = '';
            let fleetTypeId = '';

            $('select[name=route]').on('change', function() {
                routeId = $('select[name="route"]').find("option:selected").val();
                getRouteData();
            });

            $('select[name=fleet_type]').on('change', function() {
                fleetTypeId = $('select[name="fleet_type"]').find("option:selected").val();
                getRouteData();
            });

            function getRouteData() {
                if (routeId && fleetTypeId) {
                    var data = {
                        'route_id': routeId,
                        'fleet_type_id': fleetTypeId
                    }
                    $("#overlay").fadeIn(300);
                    $.ajax({
                        url: "{{ route('owner.trip.ticket.get_route_data') }}",
                        method: "get",
                        data: data,
                        success: function(result) {
                            if (result.error) {
                                $('.price-error-message').html(`<h5 class="text--danger">${result.error}</h5>`);
                                $('.submit-button').attr('disabled', 'disabled');
                                $('.price-wrapper').html(``);
                            } else {
                                $('.price-error-message').html(``);
                                $('.submit-button').removeAttr('disabled');
                                $('.price-wrapper').html(`<h5>${result}</h5>`);
                            }
                        }
                    }).done(function() {
                        setTimeout(function() {
                            $("#overlay").fadeOut(300);
                        }, 500);
                    });
                }else{
                    $('.price-wrapper').html(``);
                }
            }

            $('.main_price').on('input', function() {
                var price = $(this).val();
                $(document).find('.prices-auto').val(price);
            });
        })(jQuery)
    </script>
@endpush
