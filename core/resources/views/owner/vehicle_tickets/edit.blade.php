@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div id="overlay">
                        <div class="cv-spinner">
                            <span class="spinner"></span>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($ticketPrices->prices as $item)
                            @php $stoppages = getStoppageInfo($item->source_destination); @endphp
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <form action="{{ route('owner.trip.ticket.price.update', $item->id) }}" class="update-form">
                                    @csrf
                                    @if (
                                        $item->source_id == $ticketPrices->route->starting_point &&
                                            $item->destination_id == $ticketPrices->route->destination_point)
                                        <div class="form-group">
                                            <label for="point-{{ $loop->iteration }}">
                                                {{ $stoppages[0]->name }} - {{ $stoppages[1]->name }}
                                            </label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">
                                                    {{ $owner->general_settings->cur_sym }}
                                                </span>
                                                <input type="number" name="price" id="point-{{ $loop->iteration }}"
                                                    class="form-control prices-auto"
                                                    value="{{ getAmount($ticket_prices->price) }}" required />
                                                <button class="btn btn-primary update-price"
                                                    type="button">@lang('Update')</button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="point-{{ $loop->iteration }}">
                                                {{ $stoppages[0]->name }} - {{ $stoppages[1]->name }}
                                            </label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">
                                                    {{ $owner->general_settings->cur_sym }}
                                                </span>
                                                <input type="number" name="price" id="point-{{ $loop->iteration }}"
                                                    class="form-control prices-auto" value="{{ getAmount($item->price) }}"
                                                    required />
                                                <button class="btn btn-primary update-price"
                                                    type="button">@lang('Update')</button>
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('owner.trip.ticket.price.index') }}" />
@endpush


@push('script')
    <script>
        (function($) {
            'use strict';

            $('.update-price').on('click', function(e) {
                e.preventDefault();
                var form = $(this).parents('.update-form');
                var data = form.serialize();
                $.ajax({
                    url: form.attr('action'),
                    method: "POST",
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            notify('success', response.message);
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });
        })(jQuery)
    </script>
@endpush
