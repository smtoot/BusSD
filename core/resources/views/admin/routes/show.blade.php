@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Route Information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">{{ $route->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Operator')
                            <span class="fw-bold">{{ @$route->owner->fullname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Start Point')
                            <span class="fw-bold">{{ @$route->startingPoint->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Destination')
                            <span class="fw-bold">{{ @$route->destinationPoint->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Total Trips')
                            <span class="fw-bold">{{ $route->trips->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Route Stoppages (In Order)')</h5>
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Counter Name')</th>
                                    <th>@lang('City')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stoppages as $stoppage)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stoppage->name }}</td>
                                        <td>{{ $stoppage->city }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No stoppages defined')</td>
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

@push('breadcrumb-plugins')
    <a href="{{ route('admin.routes.index') }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back to List')
    </a>
@endpush
