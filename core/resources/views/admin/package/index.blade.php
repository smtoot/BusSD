@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Time Limit')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr>
                                        <td><span class="fw-bold">{{ __($package->name) }}</span></td>
                                        <td>{{ showAmount($package->price) }}</td>
                                        <td>{{ getPackageLimitUnit($package->time_limit, $package->unit) }}</td>
                                        <td>@php echo $package->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('admin.package.store', $package->id) }}"
                                                    data-title="@lang('Edit Package')" data-data="{{ $package }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($package->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this package?')"
                                                        data-action="{{ route('admin.package.status', $package->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this package?')"
                                                        data-action="{{ route('admin.package.status', $package->id) }}">
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
                @if ($packages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($packages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="packageModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" class="disableSubmission">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" value="" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Price')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control" name="price" value=""
                                    required>
                                <span class="input-group-text">{{ gs('cur_text') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Time Unit')</label>
                            <select class="form-control select2" data-minimum-results-for-search="-1"name="unit">
                                <option value="{{ Status::DAY }}">@lang('Days')</option>
                                <option value="{{ Status::MONTH }}">@lang('Months')</option>
                                <option value="{{ Status::YEAR }}">@lang('Year')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Time Limit')</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="time_limit" value="" required>
                                <span class="input-group-text" id="timeUnitText">@lang('Minutes')</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('admin.package.store') }}"
        data-title="@lang('Add New Package')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // Function to update the Time Limit input group text based on the selected unit
            function updateTimeUnitText(unit) {
                let timeUnitText = {
                    {{ Status::DAY }}: '@lang('Days')',
                    {{ Status::MONTH }}: '@lang('Months')',
                    {{ Status::YEAR }}: '@lang('Years')'
                };
                $('#timeUnitText').text(timeUnitText[unit]);
            }

            $('.addBtn').on('click', function() {
                var modal = $('#packageModal');

                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('[name=name]').val('');
                modal.find('[name=price]').val('');
                modal.find('[name=time_limit]').val('');
                modal.find('select[name=unit]').val({{ Status::DAY }}).change();
                updateTimeUnitText({{ Status::DAY }}); // Set default to 'Days'
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var modal = $('#packageModal');
                var data = $(this).data('data');

                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('[name=name]').val(data.name);
                modal.find('[name=price]').val(parseFloat(data.price).toFixed(2));
                modal.find('[name=time_limit]').val(data.time_limit);
                modal.find('select[name=unit]').val(data.unit).change();
                updateTimeUnitText(data.unit); // Set based on the selected unit
                modal.modal('show');
            });

            // Event listener for changing the time unit
            $('select[name=unit]').on('change', function() {
                var selectedUnit = $(this).val();
                updateTimeUnitText(selectedUnit);
            });
        })(jQuery);
    </script>
@endpush
