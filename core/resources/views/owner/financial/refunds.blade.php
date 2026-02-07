@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Trip | Ticket ID')</th>
                                    <th>@lang('Refund Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refunds as $refund)
                                    <tr>
                                        <td>{{ showDateTime($refund->created_at, 'M d, Y') }}</td>
                                        <td>
                                            {{ $refund->passenger->fullname }} <br>
                                            <small>{{ $refund->passenger->mobile }}</small>
                                        </td>
                                        <td>
                                            {{ $refund->bookedTicket->trip->title }} <br>
                                            <small>@lang('ID'): {{ $refund->bookedTicket->trx }}</small>
                                        </td>
                                        <td class="text--danger">
                                            -{{ gs('cur_sym') }}{{ getAmount($refund->amount) }}
                                        </td>
                                        <td>
                                            @php echo $refund->statusBadge; @endphp
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No refund records found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($refunds->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($refunds) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
