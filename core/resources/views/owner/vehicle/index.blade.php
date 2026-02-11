@extends('owner.layouts.app')
@section('panel')
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        {{-- ... statistics cards remain same ... --}}
        <div class="col-lg-4 col-md-6">
            <div class="card bg--primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">@lang('Total Vehicles')</h6>
                            <h3 class="text-white mb-0">{{ $totalVehicles }}</h3>
                        </div>
                        <div class="dashboard-icon">
                            <i class="las la-bus" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="card bg--success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">@lang('Active Vehicles')</h6>
                            <h3 class="text-white mb-0">{{ $activeVehicles }}</h3>
                        </div>
                        <div class="dashboard-icon">
                            <i class="las la-check-circle" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="card bg--info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">@lang('Total Capacity')</h6>
                            <h3 class="text-white mb-0">{{ $totalCapacity }}</h3>
                        </div>
                        <div class="dashboard-icon">
                            <i class="las la-users" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inactive Vehicles Warning --}}
    @if($totalVehicles > $activeVehicles)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="las la-exclamation-triangle me-2" style="font-size: 1.5rem;"></i>
        <div>
            @lang('You have') {{ $totalVehicles - $activeVehicles }} @lang('inactive vehicle(s) that won\'t appear in trip planning.')
        </div>
    </div>
    @endif

    {{-- Vehicles Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Vehicle Name')</th>
                                    <th>@lang('Model')</th>
                                    <th>@lang('License Plate')</th>
                                    <th>@lang('Total Seats')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles ?? [] as $vehicle)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="las la-bus text-muted me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <strong>{{ __($vehicle->nick_name) }}</strong>
                                                    @if($vehicle->is_vip)
                                                        <span class="badge badge--warning ms-2">VIP</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ $vehicle->fleetType->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $vehicle->brand_name }}</strong><br>
                                            <small class="text-muted">{{ $vehicle->model_no }}</small>
                                        </td>
                                        <td>
                                            <code>{{ $vehicle->registration_no }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="las la-user text-muted me-1"></i>
                                                {{ $vehicle->capacity() }} @lang('seats')
                                            </div>
                                        </td>
                                        <td>
                                            @php echo $vehicle->statusBadge; @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.vehicle.edit', $vehicle->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                
                                                @if ($vehicle->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this vehicle?')"
                                                        data-action="{{ route('owner.vehicle.status', $vehicle->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this vehicle?')"
                                                        data-action="{{ route('owner.vehicle.status', $vehicle->id) }}">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if (@$vehicles->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$vehicles) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="{{ __('Search...') }}" />
    <a href="{{ route('owner.vehicle.create') }}" class="btn btn-sm btn--primary">
        <i class="fas fa-plus"></i> @lang('Add New Vehicle')
    </a>
@endpush

