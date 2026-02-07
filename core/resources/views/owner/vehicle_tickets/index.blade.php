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
                                    <th>@lang('Fleet Type')</th>
                                    <th>@lang('Route')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketPrices ?? [] as $ticketPrice)
                                    <tr>
                                        <td>{{ __($ticketPrice->fleetType->name) }}</td>
                                        <td>{{ __($ticketPrice->route->name) }}</td>
                                        <td>@php echo $ticketPrice->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.trip.ticket.price.edit', $ticketPrice->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                @if ($ticketPrice->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this ticket price?')"
                                                        data-action="{{ route('owner.trip.ticket.price.status', $ticketPrice->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this ticket price?')"
                                                        data-action="{{ route('owner.trip.ticket.price.status', $ticketPrice->id) }}">
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
                @if (@$ticketPrices->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$ticketPrices) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.trip.ticket.price.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="fas fa-plus"></i> @lang('Add New')
    </a>
@endpush
