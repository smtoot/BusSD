@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Vehicle Info')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Nick Name')
                            <span class="fw-bold">{{ $vehicle->nick_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Registration No')
                            <span class="fw-bold">{{ $vehicle->register_no }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Operator')
                            <span class="fw-bold">{{ @$vehicle->owner->fullname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Fleet Type')
                            <span class="fw-bold">{{ @$vehicle->fleetType->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $vehicle->statusBadge @endphp
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Specifications')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Engine No')
                            <span class="fw-bold">{{ $vehicle->engine_no ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Chassis No')
                            <span class="fw-bold">{{ $vehicle->chassis_no ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Model No')
                            <span class="fw-bold">{{ $vehicle->model_no ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.fleet.vehicles') }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back')
    </a>
@endpush
