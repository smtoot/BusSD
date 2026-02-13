@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Assign Dropping Point to Routes') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dropping-points.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>{{ __('Dropping Point') }}:</strong> {{ $droppingPoint->name }} ({{ $droppingPoint->city->name ?? '-' }})
                    </div>

                    <form action="{{ route('admin.dropping-points.assign.store', $droppingPoint->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Select') }}</th>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('From') }}</th>
                                        <th>{{ __('To') }}</th>
                                        <th>{{ __('Distance') }}</th>
                                        <th>{{ __('Duration') }}</th>
                                        <th>{{ __('Drop-off Time Offset (min)') }}</th>
                                        <th>{{ __('Sort Order') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($routes as $route)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" name="routes[]" value="{{ $route->id }}" class="form-check-input"
                                                    {{ in_array($route->id, $assignedRouteIds) ? 'checked' : '' }}
                                                    onchange="toggleRouteFields(this, {{ $route->id }})">
                                            </div>
                                        </td>
                                        <td>{{ $route->id }}</td>
                                        <td>{{ $route->fromCity->name ?? '-' }}</td>
                                        <td>{{ $route->toCity->name ?? '-' }}</td>
                                        <td>{{ $route->distance }} km</td>
                                        <td>{{ $route->duration }} min</td>
                                        <td>
                                            <input type="number" name="dropoff_time_offset[{{ $route->id }}]" class="form-control"
                                                value="{{ $assignedRoutes->where('id', $route->id)->first()->pivot->dropoff_time_offset ?? 0 }}"
                                                min="0"
                                                {{ !in_array($route->id, $assignedRouteIds) ? 'disabled' : '' }}
                                                id="dropoff_time_offset_{{ $route->id }}">
                                        </td>
                                        <td>
                                            <input type="number" name="sort_order[{{ $route->id }}]" class="form-control"
                                                value="{{ $assignedRoutes->where('id', $route->id)->first()->pivot->sort_order ?? 0 }}"
                                                min="0"
                                                {{ !in_array($route->id, $assignedRouteIds) ? 'disabled' : '' }}
                                                id="sort_order_{{ $route->id }}">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No routes found.') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('Save Assignments') }}</button>
                            <a href="{{ route('admin.dropping-points.show', $droppingPoint->id) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function toggleRouteFields(checkbox, routeId) {
    const dropoffField = document.getElementById('dropoff_time_offset_' + routeId);
    const sortOrderField = document.getElementById('sort_order_' + routeId);
    
    if (checkbox.checked) {
        dropoffField.disabled = false;
        sortOrderField.disabled = false;
    } else {
        dropoffField.disabled = true;
        sortOrderField.disabled = true;
    }
}
</script>
@endsection
