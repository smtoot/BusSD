@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Requested At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refunds as $refund)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $refund->trx }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$refund->passenger->firstname }} {{ @$refund->passenger->lastname }}</span>
                                            <br>
                                            <small class="text-muted">{{ @$refund->passenger->email }}</small>
                                        </td>
                                        <td>
                                            @if(@$refund->bookedTicket->trip)
                                                {{ @$refund->bookedTicket->trip->title }}
                                                <br>
                                                <small class="text-muted">@lang('PNR'): {{ @$refund->bookedTicket->trx }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($refund->amount) }}</span>
                                        </td>
                                        <td>
                                            @if($refund->status == 0)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($refund->status == 1)
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($refund->status == 2)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($refund->created_at) }}
                                            <br>
                                            {{ diffForHumans($refund->created_at) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.refund.detail', $refund->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
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
                @if($refunds->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($refunds) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder='TRX / Passenger' />
@endpush
