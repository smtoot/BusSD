@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            @if (request()->routeIs('manager.trip.index'))
                                <thead>
                                    <tr>
                                        <th>@lang('Title')</th>
                                        <th>@lang('Schedule')</th>
                                        <th>@lang('Day Off')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($trips as $trip)
                                        <tr>
                                            <td>{{ $trip['title'] }} </td>
                                            <td>
                                                {{ showDateTime($trip->schedule->starts_from, 'H:i a') }} @lang('to')
                                                {{ showDateTime($trip->schedule->ends_at, 'H:i a') }}
                                            </td>
                                            <td>
                                                @if ($trip->day_off)
                                                    @foreach ($trip->day_off as $item)
                                                        {{ showDayOff($item) }}
                                                    @endforeach
                                                @else
                                                    @lang('No Off Day')
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('manager.sell.book', [$trip->ticket_price_id, $trip->id]) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="la la-sticky-note"></i> @lang('Book Ticket')
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            @else
                                <thead>
                                    <tr>
                                        <th>@lang('Title')</th>
                                        <th>@lang('Departure')</th>
                                        <th>@lang('Arrival')</th>
                                        <th>@lang('Fare')</th>
                                        <th>@lang('Seat Available')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($routes as $route)
                                        @forelse ($route->trips as $trip)
                                            @php
                                                if (
                                                    $route->starting_point == $trip->starting_point &&
                                                    $route->destination_point == $trip->destination_point
                                                ) {
                                                    $reverse = false;
                                                    $stoppages = $route->stoppages;
                                                } else {
                                                    $reverse = true;
                                                    $stoppages = array_reverse($route->stoppages);
                                                }
                                                $result = array_intersect($stoppages, $sdArray);
                                                $result = array_values($result);
                                                if ($result != $sdArray) {
                                                    continue;
                                                }
                                                $ticket_price = $route->ticketPrice
                                                    ->where('fleet_type_id', $trip->fleet_type_id)
                                                    ->first();
                                                $ticketPrice = $ticket_price->prices
                                                    ->where('source_destination', $sdArray)
                                                    ->first();
                                                if ($ticketPrice == null) {
                                                    $ticketPrice = $ticket_price->prices
                                                        ->where('source_destination', array_reverse($sdArray))
                                                        ->first();
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $trip['title'] }}</td>
                                                <td>
                                                    {{ showDateTime($trip->schedule->starts_from, 'h:i a') }}
                                                    <span class="d-block">{{ $trip->startingPoint->name }}</span>
                                                </td>
                                                <td>
                                                    {{ showDateTime($trip->schedule->ends_at, 'h:i a') }}
                                                    <span class="d-block">{{ $trip->destinationPoint->name }}</span>
                                                </td>
                                                <td>
                                                    {{ @$owner->general_settings->cur_sym ?? gs('cur_sym') }}{{ $ticketPrice->price }}
                                                </td>
                                                <td>
                                                    {{ collect($trip->fleetType->seats)->sum() - $trip->bookedTickets->sum('ticket_count') }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('manager.sell.book', [$ticket_price->id, $trip->id]) }}"
                                                        class="btn btn-outline--primary btn-sm">
                                                        <i class="la la-sticky-note"></i> @lang('Book Ticket')
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
                @if (request()->routeIs('manager.trip.index'))
                    @if ($trips->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($trips) }}
                        </div>
                    @endif
                @else
                    @if ($routes->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($routes) }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
