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
                                    <th>@lang('Start From')</th>
                                    <th>@lang('Ends At')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules ?? [] as $schedule)
                                    <tr>
                                        <td>{{ showDateTime($schedule->starts_from, 'h:i a') }}</td>
                                        <td>{{ showDateTime($schedule->ends_at, 'h:i a') }}</td>
                                        <td>{{ timeDifference($schedule->starts_from, $schedule->ends_at) }}</td>
                                        <td>@php echo $schedule->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('co-owner.trip.schedule.store', $schedule->id) }}"
                                                    data-title="@lang('Edit Schedule')"
                                                    data-starts_from="{{ showDateTime($schedule->starts_from, 'H:i') }}"
                                                    data-ends_at="{{ showDateTime($schedule->ends_at, 'H:i') }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                                @if ($schedule->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this schedule?')"
                                                        data-action="{{ route('co-owner.trip.schedule.status', $schedule->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this schedule?')"
                                                        data-action="{{ route('co-owner.trip.schedule.status', $schedule->id) }}">
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
                @if (@$schedules->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$schedules) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="scheduleModal" class="modal fade">
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
                            <label>@lang('Starts From')</label>
                            <input type="text" class="form-control clockpicker" placeholder="--:--" name="starts_from"
                                autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>@lang('Ends At')</label>
                            <input type="text" class="form-control clockpicker" placeholder="--:--" name="ends_at"
                                autocomplete="off">
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
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('co-owner.trip.schedule.store') }}"
        data-title="@lang('Add New Schedule')">
        <i class="fas fa-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/bootstrap-clockpicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-clockpicker.min.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let modal = $('#scheduleModal');

            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('[name=starts_from]').val('');
                modal.find('[name=ends_at]').val('');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('[name=starts_from]').val($(this).data('starts_from'));
                modal.find('[name=ends_at]').val($(this).data('ends_at'));
                modal.modal('show');
            });

            $('.clockpicker').clockpicker({
                placement: 'bottom',
                align: 'left',
                donetext: 'Done',
                autoclose: true,
            });
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .clockpicker-popover {
            z-index: 9999 !important;
        }
    </style>
@endpush
