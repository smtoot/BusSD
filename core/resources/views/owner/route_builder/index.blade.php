@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Base Route')</th>
                                    <th>@lang('Stops')</th>
                                    <th>@lang('Total Duration')</th>
                                    <th>@lang('Total Distance')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr>
                                        <td data-label="@lang('Name')">
                                            <span class="font-weight-bold">{{ $template->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $template->description }}</small>
                                        </td>
                                        <td data-label="@lang('Base Route')">
                                            {{ @$template->baseRoute->name ?: @$template->baseRoute->title ?: 'N/A' }}
                                        </td>
                                        <td data-label="@lang('Stops')">
                                            <span class="badge badge--dark">{{ $template->stops_count ?: $template->stops->count() }} @lang('Stops')</span>
                                        </td>
                                        <td data-label="@lang('Total Duration')">
                                            {{ $template->formatted_duration }}
                                        </td>
                                        <td data-label="@lang('Total Distance')">
                                            {{ getAmount($template->total_distance_km) }} @lang('KM')
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if($template->is_active)
                                                <span class="badge badge-capsule badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge-capsule badge--warning">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('owner.route.builder.edit', $template->id) }}" class="icon-btn ml-1" data-toggle="tooltip" title="@lang('Edit')">
                                                <i class="la la-pencil"></i>
                                            </a>
                                            <button class="icon-btn btn--{{ $template->is_active ? 'warning' : 'success' }} ml-1 statusBtn" data-toggle="tooltip" title="{{ $template->is_active ? __('Deactivate') : __('Activate') }}" data-action="{{ route('owner.route.builder.status', $template->id) }}">
                                                <i class="la la-eye{{ $template->is_active ? '-slash' : '' }}"></i>
                                            </button>
                                            <button class="icon-btn btn--danger ml-1 deleteBtn" data-toggle="tooltip" title="@lang('Delete')" data-action="{{ route('owner.route.builder.delete', $template->id) }}">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No route templates found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($templates->hasPages())
                    <div class="card-footer py-4">
                        {{ $templates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Confirmation Modal --}}
    <div id="statusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert')!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to change the status of this template?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Removal Confirmation')!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to delete this route template?')</p>
                        <p class="text-danger">@lang('This action cannot be undone.')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--danger">@lang('Delete')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.route.builder.create') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
        <i class="fa fa-fw fa-plus"></i> @lang('Create New Template')
    </a>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            $('.statusBtn').on('click', function () {
                var modal = $('#statusModal');
                modal.find('form').attr('action', $(this).data('action'));
                modal.modal('show');
            });
            $('.deleteBtn').on('click', function () {
                var modal = $('#deleteModal');
                modal.find('form').attr('action', $(this).data('action'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
