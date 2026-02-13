@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Dynamic Pricing Rule Details') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dynamic-pricing.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                        <a href="{{ route('admin.dynamic-pricing.edit', $pricingRule->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $pricingRule->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Owner') }}</th>
                                    <td>
                                        @if($pricingRule->owner_id == 0)
                                            {{ __('All Owners (Global)') }}
                                        @elseif($pricingRule->owner)
                                            {{ $pricingRule->owner->name }}
                                        @else
                                            {{ __('Unknown') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $pricingRule->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name (Arabic)') }}</th>
                                    <td>{{ $pricingRule->name_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Rule Type') }}</th>
                                    <td>
                                        @switch($pricingRule->rule_type)
                                            @case('surge')
                                                <span class="badge badge-danger">{{ __('Surge Pricing') }}</span>
                                                @break
                                            @case('early_bird')
                                                <span class="badge badge-success">{{ __('Early Bird Discount') }}</span>
                                                @break
                                            @case('last_minute')
                                                <span class="badge badge-warning">{{ __('Last Minute Surge') }}</span>
                                                @break
                                            @case('weekend')
                                                <span class="badge badge-info">{{ __('Weekend Pricing') }}</span>
                                                @break
                                            @case('holiday')
                                                <span class="badge badge-secondary">{{ __('Holiday Pricing') }}</span>
                                                @break
                                            @case('custom')
                                                <span class="badge badge-primary">{{ __('Custom') }}</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Operator Type') }}</th>
                                    <td>
                                        @if($pricingRule->operator_type === 'percentage')
                                            {{ __('Percentage') }}
                                        @else
                                            {{ __('Fixed Amount') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Value') }}</th>
                                    <td>
                                        @if($pricingRule->value > 0)
                                            <span class="text-danger">+{{ $pricingRule->value }}{{ $pricingRule->operator_type === 'percentage' ? '%' : '' }}</span>
                                        @else
                                            <span class="text-success">{{ $pricingRule->value }}{{ $pricingRule->operator_type === 'percentage' ? '%' : '' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Priority') }}</th>
                                    <td>{{ $pricingRule->priority }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        @if($pricingRule->status)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Valid From') }}</th>
                                    <td>{{ $pricingRule->valid_from }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Valid Until') }}</th>
                                    <td>{{ $pricingRule->valid_until }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Applicable Days') }}</th>
                                    <td>
                                        @if($pricingRule->applicable_days)
                                            @php
                                                $days = json_decode($pricingRule->applicable_days, true);
                                                $dayNames = [__('Sunday'), __('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday')];
                                            @endphp
                                            @foreach($days as $day)
                                                <span class="badge badge-info">{{ $dayNames[$day] ?? $day }}</span>
                                            @endforeach
                                        @else
                                            {{ __('All Days') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Start Time') }}</th>
                                    <td>{{ $pricingRule->start_time ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('End Time') }}</th>
                                    <td>{{ $pricingRule->end_time ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Min Hours Before Departure') }}</th>
                                    <td>{{ $pricingRule->min_hours_before_departure ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Max Hours Before Departure') }}</th>
                                    <td>{{ $pricingRule->max_hours_before_departure ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Min Seats Available') }}</th>
                                    <td>{{ $pricingRule->min_seats_available ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Max Seats Available') }}</th>
                                    <td>{{ $pricingRule->max_seats_available ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pricingRule->routes && $pricingRule->routes->count() > 0)
                    <h5 class="mt-4">{{ __('Assigned Routes') }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('From') }}</th>
                                <th>{{ __('To') }}</th>
                                <th>{{ __('Distance') }}</th>
                                <th>{{ __('Duration') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pricingRule->routes as $route)
                            <tr>
                                <td>{{ $route->id }}</td>
                                <td>{{ $route->fromCity->name ?? '-' }}</td>
                                <td>{{ $route->toCity->name ?? '-' }}</td>
                                <td>{{ $route->distance }} km</td>
                                <td>{{ $route->duration }} min</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info mt-4">
                        {{ __('No routes assigned to this pricing rule.') }}
                    </div>
                    @endif

                    @if($pricingRule->fleetTypes && $pricingRule->fleetTypes->count() > 0)
                    <h5 class="mt-4">{{ __('Assigned Fleet Types') }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Capacity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pricingRule->fleetTypes as $fleetType)
                            <tr>
                                <td>{{ $fleetType->id }}</td>
                                <td>{{ $fleetType->name }}</td>
                                <td>{{ $fleetType->capacity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
