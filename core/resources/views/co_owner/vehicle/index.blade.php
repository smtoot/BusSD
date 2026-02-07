@extends('co_owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Brand Name')</th>
                                    <th>@lang('Model')</th>
                                    <th>@lang('Registration')</th>
                                    <th>@lang('Fleet Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles ?? [] as $vehicle)
                                    <tr>
                                        <td>{{ __($vehicle->nick_name) }}</td>
                                        <td>{{ __($vehicle->brand_name) }}</td>
                                        <td>{{ $vehicle->model_no }}</td>
                                        <td>{{ $vehicle->registration_no }}</td>
                                        <td>{{ $vehicle->fleetType->name }}</td>
                                        <td>@php echo $vehicle->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button data-action="{{ route('co-owner.vehicle.store', $vehicle->id) }}"
                                                    data-title="@lang('Edit Vehicle')" data-vehicle="{{ $vehicle }}"
                                                    class="btn btn-sm btn-outline--primary editBtn">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($vehicle->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this vehicle?')"
                                                        data-action="{{ route('co-owner.vehicle.status', $vehicle->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this vehicle?')"
                                                        data-action="{{ route('co-owner.vehicle.status', $vehicle->id) }}">
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

    <div id="vehicleModal" class="modal fade">
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
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Nick Name')</label>
                                    <input type="text" name="nick_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Registration Number')</label>
                                    <input type="text" name="registration_no" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Engine Number')</label>
                                    <input type="text" name="engine_no" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Model Number')</label>
                                    <input type="text" name="model_no" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Chassis Number')</label>
                                    <input type="text" name="chasis_no" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Owner Name')</label>
                                    <input type="text" name="owner_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Owner Phone Number')</label>
                                    <input type="text" name="owner_phone" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Brand Name')</label>
                                    <input type="text" name="brand_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Fleet Type')</label>
                                    <select class="custom-select select2" data-minimum-results-for-search="-1"
                                        name="fleet_type" required>
                                        <option selected value="">@lang('Select One')</option>
                                        @foreach ($fleetTypes as $fleetType)
                                            <option value="{{ $fleetType->id }}">{{ $fleetType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('co-owner.vehicle.store') }}"
        data-title="@lang('Add New Vehicle')">
        <i class="fas fa-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let modal = $('#vehicleModal')
            let vehicle = '';
            let action = '';
            let title = '';

            $('.addBtn').on('click', function() {
                vehicle = '';
                action = $(this).data('action');
                title = $(this).data('title');
                openModal();
            });

            $('.editBtn').on('click', function() {
                vehicle = $(this).data('vehicle');
                action = $(this).data('action');
                title = $(this).data('title');
                openModal();
            });

            function openModal() {
                modal.find('form').attr('action', action);
                modal.find('.modal-title').text(title);
                modal.find('[name=nick_name]').val(vehicle.nick_name ?? '');
                modal.find('[name=registration_no]').val(vehicle.registration_no ?? '');
                modal.find('[name=engine_no]').val(vehicle.engine_no ?? '');
                modal.find('[name=model_no]').val(vehicle.model_no ?? '');
                modal.find('[name=chasis_no]').val(vehicle.chasis_no ?? '');
                modal.find('[name=owner_name]').val(vehicle.owner_name ?? '');
                modal.find('[name=owner_phone]').val(vehicle.owner_phone ?? '');
                modal.find('[name=brand_name]').val(vehicle.brand_name ?? '');
                modal.find('[name=fleet_type]').val(vehicle.fleet_type_id ?? '').change();
                modal.modal('show');
            }
        })(jQuery)
    </script>
@endpush
