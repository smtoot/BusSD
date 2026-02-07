@extends('supervisor.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Driver')</th>
                                    <th>@lang('Departure')</th>
                                    <th>@lang('Arrival')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Day Off')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trips as $item)
                                    @php
                                        $date = Carbon\Carbon::parse($item->trip->schedule->starts_from);
                                        $now = Carbon\Carbon::parse($item->trip->schedule->ends_at);
                                        $diff = $date->diff($now);
                                    @endphp
                                    <tr>
                                        <td>{{ $item->trip->title }}</td>
                                        <td>
                                            {{ $item->driver->fullname }}
                                            <br>
                                            <span>@</span>{{ $item->driver->username }}
                                        </td>
                                        <td>
                                            {{ showDateTime($item->trip->schedule->starts_from, 'h:i A') }}
                                            <br>
                                            {{ $item->trip->startingPoint->name }}
                                        </td>
                                        <td>
                                            {{ showDateTime($item->trip->schedule->ends_at, 'h:i A') }}
                                            <br>
                                            {{ $item->trip->destinationPoint->name }}
                                        </td>
                                        @if ($diff->i > 0)
                                            <td>{{ $diff->format('%h Hours %i minutes') }}
                                            </td>
                                        @else
                                            <td>{{ $diff->format('%h Hours') }}</td>
                                        @endif
                                        <td>
                                            @if ($item->trip->day_off)
                                                @foreach ($item->trip->day_off as $dayoff)
                                                    <span class="text--danger">{{ showDayOff($dayoff) }}</span>
                                                @endforeach
                                            @else
                                                @lang('No Off Day')
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('supervisor.trips.view', $item->id) }}"
                                                class="btn btn-outline--primary btn-sm">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($trips->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($trips) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
