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
                                    <th>@lang('Fleet Name')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Seat Layout')</th>
                                    <th>@lang('Total Seats')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fleetTypes as $fleetType)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $fleetType->name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$fleetType->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $fleetType->owner_id) }}"><span>@</span>{{ @$fleetType->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ @$fleetType->seatLayout->name }}</td>
                                        <td>
                                            @php
                                                $seats = (array)$fleetType->seats;
                                                echo count($seats);
                                            @endphp
                                        </td>
                                        <td>
                                            @php echo $fleetType->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.fleet.fleet_types.edit', $fleetType->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-edit"></i> @lang('Edit')
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline--danger"
                                                onclick="confirmDelete('{{ route('admin.fleet.fleet_types.delete', $fleetType->id) }}')">
                                                <i class="las la-trash"></i> @lang('Delete')
                                            </button>
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
                @if ($fleetTypes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($fleetTypes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="{{ route('admin.fleet.fleet_types.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add New Fleet Type')
        </a>
        <x-search-form placeholder="Fleet Name" />
    </div>
@endpush
