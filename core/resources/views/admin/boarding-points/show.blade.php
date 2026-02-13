@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Boarding Point Details') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.boarding-points.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                        <a href="{{ route('admin.boarding-points.edit', $boardingPoint->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.boarding-points.assign', $boardingPoint->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-route"></i> {{ __('Assign to Routes') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $boardingPoint->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Owner') }}</th>
                                    <td>
                                        @if($boardingPoint->owner_id == 0)
                                            {{ __('All Owners (Global)') }}
                                        @elseif($boardingPoint->owner)
                                            {{ $boardingPoint->owner->name }}
                                        @else
                                            {{ __('Unknown') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('City') }}</th>
                                    <td>{{ $boardingPoint->city->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Counter') }}</th>
                                    <td>{{ $boardingPoint->counter->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $boardingPoint->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name (Arabic)') }}</th>
                                    <td>{{ $boardingPoint->name_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <td>
                                        @switch($boardingPoint->type)
                                            @case('bus_stand')
                                                <span class="badge badge-primary">{{ __('Bus Stand') }}</span>
                                                @break
                                            @case('highway_pickup')
                                                <span class="badge badge-info">{{ __('Highway Pickup') }}</span>
                                                @break
                                            @case('city_center')
                                                <span class="badge badge-success">{{ __('City Center') }}</span>
                                                @break
                                            @case('airport')
                                                <span class="badge badge-warning">{{ __('Airport') }}</span>
                                                @break
                                            @case('custom')
                                                <span class="badge badge-secondary">{{ __('Custom') }}</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Sort Order') }}</th>
                                    <td>{{ $boardingPoint->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        @if($boardingPoint->status)
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
                                    <th>{{ __('Address') }}</th>
                                    <td>{{ $boardingPoint->address }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address (Arabic)') }}</th>
                                    <td>{{ $boardingPoint->address_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Latitude') }}</th>
                                    <td>{{ $boardingPoint->latitude }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Longitude') }}</th>
                                    <td>{{ $boardingPoint->longitude }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Landmark') }}</th>
                                    <td>{{ $boardingPoint->landmark ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Landmark (Arabic)') }}</th>
                                    <td>{{ $boardingPoint->landmark_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{ $boardingPoint->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $boardingPoint->email ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($boardingPoint->routes && $boardingPoint->routes->count() > 0)
                    <h5 class="mt-4">{{ __('Assigned Routes') }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('From') }}</th>
                                <th>{{ __('To') }}</th>
                                <th>{{ __('Pickup Time Offset') }}</th>
                                <th>{{ __('Sort Order') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boardingPoint->routes as $route)
                            <tr>
                                <td>{{ $route->id }}</td>
                                <td>{{ $route->fromCity->name ?? '-' }}</td>
                                <td>{{ $route->toCity->name ?? '-' }}</td>
                                <td>{{ $route->pivot->pickup_time_offset }} min</td>
                                <td>{{ $route->pivot->sort_order }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info mt-4">
                        {{ __('No routes assigned to this boarding point.') }}
                        <a href="{{ route('admin.boarding-points.assign', $boardingPoint->id) }}" class="btn btn-primary btn-sm">{{ __('Assign to Routes') }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
