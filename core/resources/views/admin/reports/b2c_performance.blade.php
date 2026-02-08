@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 mb-30">
        <div class="col-xxl-4 col-sm-6">
            <div class="card bg--primary has-link box--shadow2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-ticket-alt f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total B2C Bookings')</span>
                            <h2 class="text-white">{{ $commissions->total_bookings }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <div class="card bg--success has-link box--shadow2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-money-bill-wave f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Volume')</span>
                            <h2 class="text-white">{{ showAmount($commissions->total_volume) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <div class="card bg--info has-link box--shadow2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-percentage f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Commission')</span>
                            <h2 class="text-white">{{ showAmount($commissions->total_commission) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Commission')</th>
                                    <th>@lang('Detail')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td><strong>{{ $trx->trx }}</strong></td>
                                        <td>
                                            <span class="fw-bold">{{ @$trx->owner->fullname }}</span>
                                            <br>
                                            <small><a href="{{ route('admin.users.detail', $trx->owner_id) }}"><span>@</span>{{ @$trx->owner->username }}</a></small>
                                        </td>
                                        <td class="budget">
                                            <span class="fw-bold text--success">{{ showAmount($trx->amount) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text--primary">{{ showAmount($trx->charge) }}</span>
                                        </td>
                                        <td>{{ $trx->details }}</td>
                                        <td>{{ showDateTime($trx->created_at) }}</td>
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
                @if ($transactions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
