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
                                    <th>@lang('Layout')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seatLayouts as $seatLayout)
                                    <tr>
                                        <td>{{ $seatLayout->layout }}</td>
                                        <td>@php echo $seatLayout->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button
                                                    data-action="{{ route('co-owner.seat.layout.store', $seatLayout->id) }}"
                                                    data-title="@lang('Edit Layout')" data-seat_layout="{{ $seatLayout }}"
                                                    class="btn btn-sm btn-outline--primary editBtn">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($seatLayout->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this seat layout?')"
                                                        data-action="{{ route('co-owner.seat.layout.status', $seatLayout->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this seat layout?')"
                                                        data-action="{{ route('co-owner.seat.layout.status', $seatLayout->id) }}">
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
                @if ($seatLayouts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($seatLayouts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="seatModal" class="modal fade">
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
                            <label>@lang('Layout')</label>
                            <input type="text" name="layout" class="form-control" required />
                            <small class="text--info">
                                <i class="las la-info-circle"></i>
                                <i>
                                    @lang('Just type left and right value, a separator (x) will be added automatically')
                                </i>
                            </small>
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
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('co-owner.seat.layout.store') }}"
        data-title="@lang('Add new Layout')">
        <i class="fas fa-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let modal = $('#seatModal')
            $('.addBtn').on('click', function() {
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('[name=layout]').val('');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                let seatLayout = $(this).data('seat_layout');
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('[name=layout]').val(seatLayout.layout);
                modal.modal('show');
            });

            $(document).on('keypress', 'input[name=layout]', function(e) {
                var layout = $(this).val();
                if (layout != '') {
                    if (layout.length > 0 && layout.length <= 1)
                        $(this).val(`${layout} x `);

                    if (layout.length > 4) {
                        return false;
                    }
                }
            });

            $(document).on('keyup', 'input[name=layout]', function(e) {
                var key = event.keyCode || event.charCode;
                if (key == 8 || key == 46) {
                    $(this).val($(this).val().replace(' x ', ''));
                }

            });

        })(jQuery)
    </script>
@endpush
