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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('Manager')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($counters as $counter)
                                    <tr>
                                        <td>
                                            <strong>{{ __($counter->name) }}</strong>
                                        </td>
                                        <td>
                                            @if($counter->code)
                                                <span class="badge badge--dark">{{ $counter->code }}</span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($counter->type == 'headquarters')
                                                <span class="badge badge--success">@lang('HQ')</span>
                                            @elseif($counter->type == 'sub_branch')
                                                <span class="badge badge--info">@lang('Sub')</span>
                                            @else
                                                <span class="badge badge--primary">@lang('Branch')</span>
                                            @endif
                                        </td>
                                        <td>{{ __(@$counter->city->name) }}</td>
                                        <td>{{ @$counter->counterManager->fullname ?? __('N/A') }}</td>
                                        <td>{{ $counter->mobile }}</td>
                                        <td>@php echo $counter->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.counter.edit', $counter->id) }}" 
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>
                                                @if ($counter->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this branch?')"
                                                        data-action="{{ route('owner.counter.status', $counter->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this branch?')"
                                                        data-action="{{ route('owner.counter.status', $counter->id) }}">
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
                @if ($counters->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($counters) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="addModal" class="modal fade">
        <div class="modal-dialog" role="document">
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
                        <!-- Basic Information -->
                        <div class="form-group">
                            <label>@lang('Branch Name')</label>
                            <input type="text" name="name" class="form-control" required />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Branch Type')</label>
                                    <select name="type" class="form-control">
                                        <option value="branch" selected>@lang('Branch')</option>
                                        <option value="headquarters">@lang('Headquarters')</option>
                                        <option value="sub_branch">@lang('Sub Branch')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Autonomy Level')</label>
                                    <select name="autonomy_level" class="form-control">
                                        <option value="controlled" selected>@lang('Controlled')</option>
                                        <option value="semi_autonomous">@lang('Semi Autonomous')</option>
                                        <option value="autonomous">@lang('Autonomous')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile')</label>
                                    <input type="text" name="mobile" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') <small class="text-muted">@lang('(Optional)')</small></label>
                                    <input type="email" name="contact_email" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('City')</label>
                            <select name="city_id" class="form-control select2" required>
                                <option value="">@lang('Select City')</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ __($city->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Branch Manager')</label>
                            <select class="select2 form-control" name="counter_manager">
                                <option value="0" selected>@lang('None / Assign Later')</option>
                                @foreach ($counterManagers as $counterManager)
                                    <option value="{{ $counterManager->id }}">{{ $counterManager->fullname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Address')</label>
                            <textarea class="form-control" name="location" rows="2"></textarea>
                        </div>

                        <!-- Advanced Settings (Collapsible) -->
                        <div class="accordion" id="advancedSettings">
                            <div class="card">
                                <div class="card-header p-2" id="headingAdvanced">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdvanced" aria-expanded="false">
                                            <i class="las la-cog"></i> @lang('Advanced Settings')
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseAdvanced" class="collapse" data-bs-parent="#advancedSettings">
                                    <div class="card-body">
                                        <!-- Booking Settings -->
                                        <h6 class="mb-3">@lang('Booking Settings')</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="allows_online_booking" id="allows_online_booking" value="1" checked>
                                                    <label class="form-check-label" for="allows_online_booking">
                                                        @lang('Allow Online Booking')
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="allows_counter_booking" id="allows_counter_booking" value="1" checked>
                                                    <label class="form-check-label" for="allows_counter_booking">
                                                        @lang('Allow Counter Booking')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pricing Control -->
                                        <h6 class="mb-3 mt-3">@lang('Pricing Control')</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="can_adjust_pricing" id="can_adjust_pricing" value="1">
                                                    <label class="form-check-label" for="can_adjust_pricing">
                                                        @lang('Can Adjust Pricing')
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="small">@lang('Variance Limit (%)') </label>
                                                    <input type="number" name="pricing_variance_limit" class="form-control form-control-sm" value="0" min="0" max="100">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Route Control -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="can_set_routes" id="can_set_routes" value="1">
                                            <label class="form-check-label" for="can_set_routes">
                                                @lang('Can Set Custom Routes')
                                            </label>
                                        </div>
                                    </div>
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
    <a href="{{ route('owner.counter.create') }}" class="btn btn-sm btn--success">
        <i class="las la-plus"></i>@lang('Add New Branch')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let modal = $('#addModal');
            $('.addBtn').on('click', function() {
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));

                // Reset basic fields
                modal.find('[name=name]').val('');
                modal.find('[name=mobile]').val('');
                modal.find('[name=contact_email]').val('');
                modal.find('[name=city_id]').val('').change();
                modal.find('[name=counter_manager]').val(0).change();
                modal.find('[name=location]').val('');
                
                // Reset new fields to defaults
                modal.find('[name=type]').val('branch');
                modal.find('[name=autonomy_level]').val('controlled');
                modal.find('[name=pricing_variance_limit]').val(0);
                
                // Reset checkboxes
                modal.find('[name=allows_online_booking]').prop('checked', true);
                modal.find('[name=allows_counter_booking]').prop('checked', true);
                modal.find('[name=can_adjust_pricing]').prop('checked', false);
                modal.find('[name=can_set_routes]').prop('checked', false);
                
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));

                let counter = $(this).data('counter');

                // Populate basic fields
                modal.find('[name=name]').val(counter.name);
                modal.find('[name=mobile]').val(counter.mobile);
                modal.find('[name=contact_email]').val(counter.contact_email || '');
                modal.find('[name=city_id]').val(counter.city_id).change();
                modal.find('[name=counter_manager]').val(counter.counter_manager_id).change();
                modal.find('[name=location]').val(counter.location);
                
                // Populate new fields
                modal.find('[name=type]').val(counter.type || 'branch');
                modal.find('[name=autonomy_level]').val(counter.autonomy_level || 'controlled');
                modal.find('[name=pricing_variance_limit]').val(counter.pricing_variance_limit || 0);
                
                // Populate checkboxes
                modal.find('[name=allows_online_booking]').prop('checked', counter.allows_online_booking == 1);
                modal.find('[name=allows_counter_booking]').prop('checked', counter.allows_counter_booking == 1);
                modal.find('[name=can_adjust_pricing]').prop('checked', counter.can_adjust_pricing == 1);
                modal.find('[name=can_set_routes]').prop('checked', counter.can_set_routes == 1);
                
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush
