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
                                    <th>@lang('Apply To')</th>
                                    <th>@lang('Scope')</th>
                                    <th>@lang('Value')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($modifiers as $modifier)
                                    <tr>
                                        <td data-label="@lang('Name')">
                                            <span class="font-weight-bold">{{ $modifier->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $modifier->description }}</small>
                                        </td>
                                        <td data-label="@lang('Apply To')">
                                            @php
                                                $appliesTo = match($modifier->applies_to) {
                                                    'category' => 'Category',
                                                    'position' => 'Position',
                                                    'specific_seats' => 'Specific Seats',
                                                    'all' => 'All Seats',
                                                    default => $modifier->applies_to
                                                };
                                            @endphp
                                            <span class="badge badge--dark">{{ $appliesTo }}</span>
                                            @if($modifier->applies_to == 'position')
                                                <br>
                                                <small>
                                                    @if($modifier->row_range_start) Row {{ $modifier->row_range_start }}-{{ $modifier->row_range_end }} @endif
                                                    @if($modifier->seat_type) | {{ ucfirst($modifier->seat_type) }} @endif
                                                </small>
                                            @elseif($modifier->applies_to == 'specific_seats')
                                                <br>
                                                <small>{{ implode(', ', (array)$modifier->seat_positions) }}</small>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Scope')">
                                            @if($modifier->trip_id)
                                                <span class="text--primary">Trip: {{ @$modifier->trip->title }}</span>
                                            @elseif($modifier->fleet_type_id)
                                                <span class="text--info">Fleet: {{ @$modifier->fleetType->name }}</span>
                                            @else
                                                <span class="badge badge--primary">Global</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Value')">
                                            @php
                                                $prefix = ($modifier->modifier_value >= 0) ? '+' : '';
                                                $suffix = ($modifier->modifier_type == 'percentage') ? '%' : ' ' . gs('cur_text');
                                            @endphp
                                            <span class="font-weight-bold {{ $modifier->modifier_value >= 0 ? 'text--success' : 'text--danger' }}">
                                                {{ $prefix }}{{ getAmount($modifier->modifier_value) }}{{ $suffix }}
                                            </span>
                                        </td>
                                        <td data-label="@lang('Priority')">
                                            <span class="badge badge--light">{{ $modifier->priority }}</span>
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if($modifier->is_active)
                                                <span class="badge badge-capsule badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge-capsule badge--warning">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('owner.seat.pricing.form', $modifier->id) }}" class="icon-btn ml-1" data-toggle="tooltip" title="@lang('Edit')">
                                                <i class="la la-pencil"></i>
                                            </a>
                                            <button class="icon-btn btn--{{ $modifier->is_active ? 'warning' : 'success' }} ml-1 statusBtn" data-toggle="tooltip" title="{{ $modifier->is_active ? __('Deactivate') : __('Activate') }}" data-action="{{ route('owner.seat.pricing.status', $modifier->id) }}">
                                                <i class="la la-eye{{ $modifier->is_active ? '-slash' : '' }}"></i>
                                            </button>
                                            <button class="icon-btn btn--danger ml-1 deleteBtn" data-toggle="tooltip" title="@lang('Delete')" data-action="{{ route('owner.seat.pricing.delete', $modifier->id) }}">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No seat pricing modifiers found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($modifiers->hasPages())
                    <div class="card-footer py-4">
                        {{ $modifiers->links() }}
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
                        <p>@lang('Are you sure you want to change the status of this modifier?')</p>
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
                        <p>@lang('Are you sure you want to delete this seat pricing modifier?')</p>
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
    <a href="{{ route('owner.seat.pricing.form') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
        <i class="fa fa-fw fa-plus"></i> @lang('Add New Modifier')
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
