@extends('admin.layouts.app')
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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($features as $feature)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($feature->name) }}</span>
                                        </td>
                                        <td>@php echo $feature->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn"
                                                    data-action="{{ route('admin.feature.store', $feature->id) }}"
                                                    data-title="@lang('Edit Feature')" data-data="{{ $feature }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($feature->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this feature?')"
                                                        data-action="{{ route('admin.feature.status', $feature->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this feature?')"
                                                        data-action="{{ route('admin.feature.status', $feature->id) }}">
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
                @if ($features->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($features) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="featureModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="featureModalLabel"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="disableSubmission" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" value="" autocomplete="off"
                                required>
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
    <button class="btn btn-sm btn-outline--primary addBtn" data-action="{{ route('admin.feature.store') }}"
        data-title="@lang('Add New Feature')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addBtn').on('click', function() {
                var modal = $('#featureModal');
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('#featureModalLabel').text($(this).data('title'));
                modal.find('[name=name]').val('');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var modal = $('#featureModal');
                var data = $(this).data('data');
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('#featureModalLabel').text($(this).data('title'));
                modal.find('[name=name]').val(data.name);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
