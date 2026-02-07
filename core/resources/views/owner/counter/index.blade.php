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
                                            {{ __($counter->name) }}
                                        </td>
                                        <td>{{ __($counter->city) }}</td>
                                        <td>{{ @$counter->counterManager->fullname ?? 'N/A' }}</td>
                                        <td>{{ $counter->mobile }}</td>
                                        <td>@php echo $counter->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button data-action="{{ route('owner.counter.store', $counter->id) }}"
                                                    data-title="@lang('Edit Counter')" data-counter="{{ $counter }}"
                                                    class="btn btn-sm btn-outline--primary editBtn">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($counter->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this counter?')"
                                                        data-action="{{ route('owner.counter.status', $counter->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this counter?')"
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
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Mobile')</label>
                            <input type="number" name="mobile" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('City')</label>
                            <input type="text" name="city" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Counter Managers')</label>
                            <select class="select2 form-control" name="counter_manager">
                                <option value="0" selected>@lang('Select One')</option>
                                @foreach ($counterManagers as $counterManager)
                                    <option value="{{ $counterManager->id }}">{{ $counterManager->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Address')</label>
                            <textarea class="form-control" name="location" rows="3"></textarea>
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
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('owner.counter.store') }}"
        data-title="@lang('Add New Counter')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let modal = $('#addModal');
            $('.addBtn').on('click', function() {
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));

                modal.find('[name=name]').val('');
                modal.find('[name=mobile]').val('');
                modal.find('[name=city]').val('');
                modal.find('[name=counter_manager]').val(0).change();
                modal.find('[name=location]').val('');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));

                let counter = $(this).data('counter');

                modal.find('[name=name]').val(counter.name);
                modal.find('[name=mobile]').val(counter.mobile);
                modal.find('[name=city]').val(counter.city);
                modal.find('[name=counter_manager]').val(counter.counter_manager_id).change();
                modal.find('[name=location]').val(counter.location);
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush
