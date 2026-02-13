@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body p-0">
                    <div class="p-3 bg--white">
                        <div class="mt-15">
                            <h4 class="">{{ $trip->title }}</h4>
                            <span class="text--small">@lang('Owned by') <a href="{{ route('admin.users.detail', $trip->owner_id) }}"><span>@</span>{{ @$trip->owner->username }}</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Trip Information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $trip->statusBadge @endphp
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Route')
                            <span class="fw-bold">{{ @$trip->route->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Starting City')
                            <span class="fw-bold">{{ @$trip->startingPoint->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Destination City')
                            <span class="fw-bold">{{ @$trip->destinationPoint->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Fleet Type')
                            <span class="fw-bold">{{ @$trip->fleetType->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Schedule')
                            <span class="fw-bold">{{ @$trip->schedule->start_from }} - {{ @$trip->schedule->end_at }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Trip Operations')</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">@lang('Assigned Vehicle')</h6>
                            <p class="fw-bold">{{ @$trip->vehicle->nick_name ?? 'N/A' }} ({{ @$trip->vehicle->register_no ?? 'N/A' }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.trips.index') }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back to List')
    </a>
@endpush
