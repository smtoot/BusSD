@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Delete Dynamic Pricing Rule') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dynamic-pricing.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> {{ __('Warning') }}</h5>
                        <p>{{ __('Are you sure you want to delete this dynamic pricing rule? This action cannot be undone.') }}</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Rule Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $pricingRule->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $pricingRule->name }}</td>
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
                                    <th>{{ __('Valid From') }}</th>
                                    <td>{{ $pricingRule->valid_from }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Valid Until') }}</th>
                                    <td>{{ $pricingRule->valid_until }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pricingRule->routes && $pricingRule->routes->count() > 0)
                    <div class="alert alert-danger mt-3">
                        <h5><i class="fas fa-exclamation-circle"></i> {{ __('Routes Affected') }}</h5>
                        <p>{{ __('This pricing rule is assigned to the following routes:') }}</p>
                        <ul>
                            @foreach($pricingRule->routes as $route)
                            <li>{{ $route->fromCity->name ?? '-' }} → {{ $route->toCity->name ?? '-' }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.dynamic-pricing.destroy', $pricingRule->id) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete It') }}</button>
                        <a href="{{ route('admin.dynamic-pricing.show', $pricingRule->id) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
