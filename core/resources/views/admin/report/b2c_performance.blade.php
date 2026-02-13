@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-chart-bar overlay-icon text--primary"></i>
                <div class="widget-two__content">
                    <h2 class="gross-volume">{{ gs('cur_sym') }}{{ getAmount($commissions->total_volume ?? 0) }}</h2>
                    <p>@lang('Total App Gross Volume')</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-hand-holding-usd overlay-icon text--success"></i>
                <div class="widget-two__content">
                    <h2 class="commission-earned">{{ gs('cur_sym') }}{{ getAmount($commissions->total_commission ?? 0) }}</h2>
                    <p>@lang('Total Platform Commission')</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-ticket-alt overlay-icon text--info"></i>
                <div class="widget-two__content">
                    <h2 class="total-bookings">{{ $commissions->total_bookings ?? 0 }}</h2>
                    <p>@lang('Total App Bookings')</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-30">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Gross Amount')</th>
                                    <th>@lang('Platform Fee')</th>
                                    <th>@lang('Net to Operator')</th>
                                    <th>@lang('TRX')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>{{ showDateTime($trx->created_at) }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $trx->owner_id) }}">{{ $trx->owner->fullname }}</a>
                                        </td>
                                        <td class="fw-bold">{{ gs('cur_sym') }}{{ getAmount($trx->amount + $trx->charge) }}</td>
                                        <td class="text--danger">{{ gs('cur_sym') }}{{ getAmount($trx->charge) }}</td>
                                        <td class="text--success fw-bold">{{ gs('cur_sym') }}{{ getAmount($trx->amount) }}</td>
                                        <td><strong>{{ $trx->trx }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No App transactions recorded yet')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
