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
                                    <th>@lang('Number of Deck')</th>
                                    <th>@lang('Seat Layout')</th>
                                    <th>@lang('Total Seat')</th>
                                    <th>@lang('AC / Non AC')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fleetTypes as $fleetType)
                                    <tr>
                                        <td>{{ __($fleetType->name) }}</td>
                                        <td>{{ $fleetType->deck }}</td>
                                        <td>{{ $fleetType->seatLayout->name ?: $fleetType->seatLayout->layout }}</td>
                                        <td>{{ array_sum((array)$fleetType->seats) }}</td>
                                        <td>{{ $fleetType->has_ac ? __('AC') : __('Non AC') }}</td>
                                        <td>@php echo $fleetType->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                @if ($fleetType->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this fleet type?')"
                                                        data-action="{{ route('owner.fleet.type.status', $fleetType->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this fleet type?')"
                                                        data-action="{{ route('owner.fleet.type.status', $fleetType->id) }}">
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
                @if ($fleetTypes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($fleetTypes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="fleetModal" class="modal fade">
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
                            <label>@lang('Seat Layout')</label>
                            <select name="seat_layout" class="form-control select2" data-minimum-results-for-search="-1">
                                <option value="0" selected>@lang('Select One')</option>
                                @foreach ($seatLayouts as $item)
                                    <option value="{{ $item->id }}">{{ __($item->layout) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Number of Deck')</label>
                            <input type="number" name="deck" class="form-control" placeholder="@lang('Example: 1 / 2')"
                                required />
                        </div>
                        <div class="seat-number-wrapper"></div>
                        <div class="form-group">
                            <label>@lang('Has AC')</label>
                            <select name="has_ac" class="form-control select2" data-minimum-results-for-search="-1">
                                <option value="{{ Status::YES }}">@lang('Yes')</option>
                                <option value="{{ Status::NO }}">@lang('No')</option>
                            </select>
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
    @endpush
