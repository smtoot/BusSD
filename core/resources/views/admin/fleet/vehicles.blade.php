@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Vehicle Info')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Fleet Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $vehicle->nick_name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $vehicle->register_no }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$vehicle->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $vehicle->owner_id) }}"><span>@</span>{{ @$vehicle->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ @$vehicle->fleetType->name }}</td>
                                        <td>
                                            @php echo $vehicle->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.fleet.vehicles.show', $vehicle->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($vehicles->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($vehicles) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="Nick Name / Reg No" />
        <a href="{{ route('admin.fleet.export') }}" class="btn btn-outline--primary"><i class="las la-download"></i> @lang('Export')</a>
    </div>
@endpush
