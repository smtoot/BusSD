@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Registration No.')</th>
                                    <th>@lang('Vehicle Name')</th>
                                    <th>@lang('Driver')</th>
                                    <th>@lang('Supervisor')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedVehicles ?? [] as $assignedVehicle)
                                    <tr>
                                        <td>{{ $assignedVehicle->vehicle->registration_no }}</td>
                                        <td>{{ $assignedVehicle->vehicle->nick_name }}</td>
                                        <td>
                                            {{ $assignedVehicle->driver->fullname }}
                                            <br>
                                            <span>@</span>{{ $assignedVehicle->driver->username }}
                                        </td>
                                        <td>
                                            {{ $assignedVehicle->supervisor->fullname }}
                                            <br>
                                            <span>@</span>{{ $assignedVehicle->supervisor->username }}
                                        </td>
                                        <td>{{ $assignedVehicle->trip->title }}</td>
                                        <td>@php echo $assignedVehicle->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('owner.trip.assign.vehicle.store', $assignedVehicle->id) }}"
                                                    data-title="@lang('Edit Assigned Vehicle')"
                                                    data-assigned_vehicle="{{ $assignedVehicle }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                                @if ($assignedVehicle->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this assigned vehicle?')"
                                                        data-action="{{ route('owner.trip.assign.vehicle.status', $assignedVehicle->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this assigned vehicle?')"
                                                        data-action="{{ route('owner.trip.assign.vehicle.status', $assignedVehicle->id) }}">
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
                @if (@$assignedVehicles->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$assignedVehicles) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="assignVehicleModal" class="modal fade">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Trip')</label>
                            <select class="select2 form-control" name="trip" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($trips as $trip)
                                    <option value="{{ $trip->id }}" data-vehicles="{{ $trip->fleetType->vehicles }}">
                                        {{ $trip->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Bus Registration Number')</label>
                            <select class="select2 form-control" name="bus_registration_number" required>
                                <option selected value="">@lang('Select One')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Driver')</label>
                            <select class="select2 from-control" name="driver" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}" data-name="{{ $driver->name }}">
                                        {{ $driver->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Supervisor')</label>
                            <select class="select2 form-control" name="supervisor" required>
                                <option selected value="">@lang('Select One')</option>
                                @foreach ($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}" data-name="{{ $supervisor->name }}">
                                        {{ $supervisor->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('owner.trip.assign.vehicle.store') }}"
        data-title="@lang('Assign New Vehicle')">
        <i class="fas fa-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let modal = $('#assignVehicleModal');

            $('.addBtn').on('click', function() {
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('select[name=trip]').val('').change();
                modal.find('select[name=bus_registration_number]').val('').change();
                modal.find('select[name=driver]').val('').change();
                modal.find('select[name=supervisor]').val('').change();
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                let assignedVehicle = $(this).data('assigned_vehicle');

                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('select[name=trip]').val(assignedVehicle.trip_id).change();
                updateVehicleRegistrationNumber();
                modal.find('select[name=bus_registration_number]').val(assignedVehicle.vehicle_id).change();
                modal.find('select[name=driver]').val(assignedVehicle.driver_id).change();
                modal.find('select[name=supervisor]').val(assignedVehicle.supervisor_id).change();
                modal.modal('show');
            });

            $('select[name="trip"]').on('change', function() {
                updateVehicleRegistrationNumber();
            });

            function updateVehicleRegistrationNumber() {
                var vehicles = $('select[name="trip"]').find("option:selected").data('vehicles');
                var options = `<option selected value="">@lang('Select One')</option>`
                $.each(vehicles, function(index, vehicle) {
                    options += `<option value="${vehicle.id}" data-name="${vehicle.registration_no}">
                                    ${vehicle.registration_no} (${vehicle.nick_name})
                                </option>`
                });
                $('select[name=bus_registration_number]').html(options);
            }
        })(jQuery)
    </script>
@endpush
