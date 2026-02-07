@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12 mb-4">
            <h5 class="mb-3">@lang('Route Performance')</h5>
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table text-center">
                            <thead>
                                <tr>
                                    <th>@lang('Route Name')</th>
                                    <th>@lang('Total Bookings')</th>
                                    <th>@lang('Total Revenue')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routePerformance as $route)
                                    <tr>
                                        <td>{{ $route->route_name }}</td>
                                        <td>{{ $route->total_bookings }}</td>
                                        <td class="fw-bold">{{ gs('cur_sym') }}{{ getAmount($route->total_revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No performance data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <h5 class="mb-3">@lang('Trip Performance')</h5>
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table text-center">
                            <thead>
                                <tr>
                                    <th>@lang('Trip Title')</th>
                                    <th>@lang('Total Bookings')</th>
                                    <th>@lang('Total Revenue')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tripPerformance as $trip)
                                    <tr>
                                        <td>{{ $trip->trip_title }}</td>
                                        <td>{{ $trip->total_bookings }}</td>
                                        <td class="fw-bold">{{ gs('cur_sym') }}{{ getAmount($trip->total_revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No performance data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
