@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">@lang('Route Analytics')</h5>
                        
                        {{-- Date Range Filter --}}
                        <form action="{{ route('owner.analytics.index') }}" method="GET" class="d-flex gap-2">
                            <input type="date" name="start_date" class="form-control form-control-sm" 
                                   value="{{ $startDate }}" required>
                            <input type="date" name="end_date" class="form-control form-control-sm" 
                                   value="{{ $endDate }}" required>
                            <button type="submit" class="btn btn-sm btn--primary">
                                <i class="las la-search"></i> @lang('Filter')
                            </button>
                        </form>
                    </div>
                    
                    {{-- Top Routes Performance --}}
                    @if($topRoutes && $topRoutes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('Route')</th>
                                        <th>@lang('Total Trips')</th>
                                        <th>@lang('Revenue')</th>
                                        <th>@lang('Costs')</th>
                                        <th>@lang('Profit')</th>
                                        <th>@lang('Margin')</th>
                                        <th>@lang('Occupancy')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topRoutes as $item)
                                        @php
                                            $route = $item['route'];
                                            $data = $item['data'];
                                            $profitClass = $data['profit']['profit_margin'] >= 20 ? 'text-success' : 
                                                          ($data['profit']['profit_margin'] >= 0 ? 'text-warning' : 'text-danger');
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $route->title }}</strong><br>
                                                <small class="text-muted">{{ $route->distance }} km</small>
                                            </td>
                                            <td>{{ $data['operational']['total_trips'] }}</td>
                                            <td>{{ gs('cur_sym') }}{{ getAmount($data['revenue']['net']) }}</td>
                                            <td>{{ gs('cur_sym') }}{{ getAmount($data['costs']['total']) }}</td>
                                            <td class="{{ $profitClass }}">
                                                <strong>{{ gs('cur_sym') }}{{ getAmount($data['profit']['gross_profit']) }}</strong>
                                            </td>
                                            <td class="{{ $profitClass }}">
                                                <strong>{{ number_format($data['profit']['profit_margin'], 1) }}%</strong>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $data['occupancy']['avg_occupancy_rate'] }}%"
                                                         aria-valuenow="{{ $data['occupancy']['avg_occupancy_rate'] }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($data['occupancy']['avg_occupancy_rate'], 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('owner.analytics.route', $route->id) }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                                   class="btn btn-sm btn--primary">
                                                    <i class="las la-eye"></i> @lang('Details')
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="las la-info-circle"></i> @lang('No route data available for the selected period.')
                        </div>
                    @endif
                    
                    {{-- All Routes Selector --}}
                    <div class="mt-4">
                        <h6>@lang('View Other Routes')</h6>
                        <div class="row">
                            @foreach($routes as $route)
                                <div class="col-md-4 mb-2">
                                    <a href="{{ route('owner.analytics.route', $route->id) }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                       class="btn btn-outline--primary btn-block">
                                        {{ $route->title }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
