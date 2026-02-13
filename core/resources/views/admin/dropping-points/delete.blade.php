@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Delete Dropping Point') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dropping-points.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> {{ __('Warning') }}</h5>
                        <p>{{ __('Are you sure you want to delete this dropping point? This action cannot be undone.') }}</p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Dropping Point Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $droppingPoint->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $droppingPoint->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('City') }}</th>
                                    <td>{{ $droppingPoint->city->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td>{{ $droppingPoint->address }}</td>
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
                            </table>
                        </div>
                    </div>

                    @if($droppingPoint->routes && $droppingPoint->routes->count() > 0)
                    <div class="alert alert-danger mt-3">
                        <h5><i class="fas fa-exclamation-circle"></i> {{ __('Routes Affected') }}</h5>
                        <p>{{ __('This dropping point is assigned to the following routes:') }}</p>
                        <ul>
                            @foreach($droppingPoint->routes as $route)
                            <li>{{ $route->fromCity->name ?? '-' }} → {{ $route->toCity->name ?? '-' }} (Offset: {{ $route->pivot->dropoff_time_offset }} min)</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.dropping-points.destroy', $droppingPoint->id) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete It') }}</button>
                        <a href="{{ route('admin.dropping-points.show', $droppingPoint->id) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
