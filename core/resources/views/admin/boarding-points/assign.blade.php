@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Assign Boarding Points to Route')</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.boarding-points.assignStore', $route->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert--info mb-3">
                                    <i class="las la-info-circle"></i>
                                    <strong>@lang('Route Information')</strong>
                                </div>
                                <div class="card border--primary">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>@lang('Route Name')</label>
                                                <span class="fw-bold">{{ $route->name }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label>@lang('Starting City')</label>
                                                <span>{{ @$route->startingPoint->name }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label>@lang('Destination City')</label>
                                                <span>{{ @$route->destinationPoint->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border--primary mt-3">
                            <div class="card-body">
                                <h5 class="mb-20 text-muted">@lang('Available Boarding Points')</h5>
                                <div class="table-responsive--md table-responsive">
                                    <table class="table table--light style--two">
                                        <thead>
                                            <tr>
                                                <th>@lang('Select')</th>
                                                <th>@lang('Boarding Point Name')</th>
                                                <th>@lang('Type')</th>
                                                <th>@lang('City')</th>
                                                <th>@lang('Landmark')</th>
                                                <th>@lang('Address')</th>
                                                <th>@lang('Contact')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($boardingPoints as $boardingPoint)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" name="boarding_point_ids[]" value="{{ $boardingPoint->id }}" id="bp_{{ $boardingPoint->id }}" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold">{{ $boardingPoint->name }}</span>
                                                        <br>
                                                        <span class="small text-muted">
                                                            {{ $boardingPoint->getTypeLabelAttribute() }}
                                                            @if($boardingPoint->city)
                                                                | {{ @$boardingPoint->city->name }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>{{ $boardingPoint->landmark }}</td>
                                                    <td>{{ $boardingPoint->address }}</td>
                                                    <td>
                                                        @if($boardingPoint->contact_phone || $boardingPoint->contact_email)
                                                            {{ $boardingPoint->contact_phone }}
                                                            @if($boardingPoint->contact_email)
                                                                <br>{{ $boardingPoint->contact_email }}
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" name="pickup_time_offsets[]" value="0" min="0" class="form-control" style="width: 80px;" placeholder="0" />
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn--primary">@lang('Assign Boarding Points')</button>
                            <a href="{{ route('admin.routes.show', $route->id) }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
