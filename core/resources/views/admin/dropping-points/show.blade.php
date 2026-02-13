@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Dropping Point Details') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dropping-points.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                        <a href="{{ route('admin.dropping-points.edit', $droppingPoint->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.dropping-points.assign', $droppingPoint->id) }}" class="btn btn-success btn-sm">
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
                                    <td>{{ $droppingPoint->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Owner') }}</th>
                                    <td>
                                        @if($droppingPoint->owner_id == 0)
                                            {{ __('All Owners (Global)') }}
                                        @elseif($droppingPoint->owner)
                                            {{ $droppingPoint->owner->name }}
                                        @else
                                            {{ __('Unknown') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('City') }}</th>
                                    <td>{{ $droppingPoint->city->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $droppingPoint->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name (Arabic)') }}</th>
                                    <td>{{ $droppingPoint->name_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <td>
                                        @switch($droppingPoint->type)
                                            @case('bus_stand')
                                                <span class="badge badge-primary">{{ __('Bus Stand') }}</span>
                                                @break
                                            @case('city_center')
                                                <span class="badge badge-info">{{ __('City Center') }}</span>
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
                                    <td>{{ $droppingPoint->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        @if($droppingPoint->status)
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
                                    <td>{{ $droppingPoint->address }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address (Arabic)') }}</th>
                                    <td>{{ $droppingPoint->address_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Latitude') }}</th>
                                    <td>{{ $droppingPoint->latitude }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Longitude') }}</th>
                                    <td>{{ $droppingPoint->longitude }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Landmark') }}</th>
                                    <td>{{ $droppingPoint->landmark ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Landmark (Arabic)') }}</th>
                                    <td>{{ $droppingPoint->landmark_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{ $droppingPoint->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{ $droppingPoint->email ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($droppingPoint->routes && $droppingPoint->routes->count() > 0)
                    <h5 class="mt-4">{{ __('Assigned Routes') }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('From') }}</th>
                                <th>{{ __('To') }}</th>
                                <th>{{ __('Drop-off Time Offset') }}</th>
                                <th>{{ __('Sort Order') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($droppingPoint->routes as $route)
                            <tr>
                                <td>{{ $route->id }}</td>
                                <td>{{ $route->fromCity->name ?? '-' }}</td>
                                <td>{{ $route->toCity->name ?? '-' }}</td>
                                <td>{{ $route->pivot->dropoff_time_offset }} min</td>
                                <td>{{ $route->pivot->sort_order }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info mt-4">
                        {{ __('No routes assigned to this dropping point.') }}
                        <a href="{{ route('admin.dropping-points.assign', $droppingPoint->id) }}" class="btn btn-primary btn-sm">{{ __('Assign to Routes') }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
