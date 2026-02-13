@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">{{ $route->title }}</h5>
                            <p class="text-muted mb-0">
                                <i class="las la-route"></i> {{ $route->distance }} km | 
                                @lang('Period'): {{ $startDate }} @lang('to') {{ $endDate }}
                            </p>
                        </div>
                        <a href="{{ route('owner.analytics.index') }}" class="btn btn-sm btn--secondary">
                            <i class="las la-arrow-left"></i> @lang('Back')
                        </a>
                    </div>
                    
                    {{-- Summary Cards --}}
                    <div class="row mb-4">
                        {{-- Revenue Card --}}
                        <div class="col-md-3">
                            <div class="card border--primary">
                                <div class="card-body text-center">
                                    <i class="las la-dollar-sign text-primary" style="font-size: 2.5rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ gs('cur_sym') }}{{ getAmount($analytics['revenue']['net']) }}</h3>
                                    <p class="text-muted mb-0">@lang('Net Revenue')</p>
                                    <small class="text-muted">
                                        @lang('Bookings'): {{ $analytics['revenue']['total_bookings'] }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Costs Card --}}
                        <div class="col-md-3">
                            <div class="card border--warning">
                                <div class="card-body text-center">
                                    <i class="las la-wallet text-warning" style="font-size: 2.5rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ gs('cur_sym') }}{{ getAmount($analytics['costs']['total']) }}</h3>
                                    <p class="text-muted mb-0">@lang('Total Costs')</p>
                                    <small class="text-muted">
                                        @lang('Trips'): {{ $analytics['operational']['total_trips'] }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Profit Card --}}
                        <div class="col-md-3">
                            <div class="card border--{{ $analytics['profit']['profit_margin'] >= 0 ? 'success' : 'danger' }}">
                                <div class="card-body text-center">
                                    <i class="las la-chart-line text-{{ $analytics['profit']['profit_margin'] >= 0 ? 'success' : 'danger' }}" 
                                       style="font-size: 2.5rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ gs('cur_sym') }}{{ getAmount($analytics['profit']['gross_profit']) }}</h3>
                                    <p class="text-muted mb-0">@lang('Gross Profit')</p>
                                    <small class="text-muted">
                                        @lang('Margin'): {{ number_format($analytics['profit']['profit_margin'], 1) }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Occupancy Card --}}
                        <div class="col-md-3">
                            <div class="card border--info">
                                <div class="card-body text-center">
                                    <i class="las la-users text-info" style="font-size: 2.5rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ number_format($analytics['occupancy']['avg_occupancy_rate'], 1) }}%</h3>
                                    <p class="text-muted mb-0">@lang('Avg Occupancy')</p>
                                    <small class="text-muted">
                                        @lang('Sold'): {{ $analytics['occupancy']['total_seats_sold'] }}/{{ $analytics['occupancy']['total_seats_available'] }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Detailed Breakdown --}}
                    <div class="row">
                        {{-- Revenue Breakdown --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg--primary">
                                    <h6 class="card-title text-white mb-0">@lang('Revenue Breakdown')</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>@lang('Gross Revenue')</td>
                                            <td class="text-end"><strong>{{ gs('cur_sym') }}{{ getAmount($analytics['revenue']['gross']) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Platform Fees')</td>
                                            <td class="text-end text-danger">-{{ gs('cur_sym') }}{{ getAmount($analytics['revenue']['platform_fees']) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>@lang('Net Revenue')</strong></td>
                                            <td class="text-end"><strong>{{ gs('cur_sym') }}{{ getAmount($analytics['revenue']['net']) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Avg Ticket Price')</td>
                                            <td class="text-end">{{ gs('cur_sym') }}{{ getAmount($analytics['revenue']['avg_ticket_price']) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Cost Breakdown --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg--warning">
                                    <h6 class="card-title text-white mb-0">@lang('Cost Breakdown')</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>@lang('Fuel Cost')</td>
                                            <td class="text-end">{{ gs('cur_sym') }}{{ getAmount($analytics['costs']['fuel']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Driver Cost')</td>
                                            <td class="text-end">{{ gs('cur_sym') }}{{ getAmount($analytics['costs']['driver']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Maintenance')</td>
                                            <td class="text-end">{{ gs('cur_sym') }}{{ getAmount($analytics['costs']['maintenance']) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>@lang('Total Cost')</strong></td>
                                            <td class="text-end"><strong>{{ gs('cur_sym') }}{{ getAmount($analytics['costs']['total']) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Profitability Metrics --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg--success">
                                    <h6 class="card-title text-white mb-0">@lang('Profitability Metrics')</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>@lang('Gross Profit')</td>
                                            <td class="text-end"><strong>{{ gs('cur_sym') }}{{ getAmount($analytics['profit']['gross_profit']) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Profit Margin')</td>
                                            <td class="text-end"><strong>{{ number_format($analytics['profit']['profit_margin'], 2) }}%</strong></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Profit Per Trip')</td>
                                            <td class="text-end">{{ gs('cur_sym') }}{{ getAmount($analytics['profit']['profit_per_trip']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Profit Per KM')</td>
                                            <td class="text-end">{{ gs('cur_sym') }}{{ getAmount($analytics['profit']['profit_per_km']) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Operations Summary --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg--info">
                                    <h6 class="card-title text-white mb-0">@lang('Operations Summary')</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>@lang('Total Trips')</td>
                                            <td class="text-end"><strong>{{ $analytics['operational']['total_trips'] }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Completed Trips')</td>
                                            <td class="text-end text-success">{{ $analytics['operational']['completed_trips'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Cancelled Trips')</td>
                                            <td class="text-end text-danger">{{ $analytics['operational']['cancelled_trips'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Cancellation Rate')</td>
                                            <td class="text-end">{{ number_format($analytics['operational']['cancellation_rate'], 2) }}%</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Occupancy Details --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">@lang('Seat Utilization')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h4>{{ $analytics['occupancy']['total_seats_available'] }}</h4>
                                            <p class="text-muted">@lang('Total Seats Available')</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-success">{{ $analytics['occupancy']['total_seats_sold'] }}</h4>
                                            <p class="text-muted">@lang('Seats Sold')</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-danger">{{ $analytics['occupancy']['empty_seats'] }}</h4>
                                            <p class="text-muted">@lang('Empty Seats')</p>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $analytics['occupancy']['avg_occupancy_rate'] }}%">
                                            {{ number_format($analytics['occupancy']['avg_occupancy_rate'], 1) }}% @lang('Filled')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
