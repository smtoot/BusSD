@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-money-bill-wave overlay-icon text--primary"></i>
                <div class="widget-two__content">
                    <h2 class="text--primary">{{ gs('cur_sym') }}{{ getAmount($totalB2C) }}</h2>
                    <p>@lang('Gross B2C Sale')</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-percentage overlay-icon text--danger"></i>
                <div class="widget-two__content">
                    <h2 class="text--danger">{{ gs('cur_sym') }}{{ getAmount($totalCommission) }}</h2>
                    <p>@lang('Platform Commission') ({{ $commissionRate }}%)</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-wallet overlay-icon text--success"></i>
                <div class="widget-two__content">
                    <h2 class="text--success">{{ gs('cur_sym') }}{{ getAmount($netEarnings) }}</h2>
                    <p>@lang('Net Earnings (Operator)')</p>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-4">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Trip | Date')</th>
                                    <th>@lang('Gross Sale')</th>
                                    <th>@lang('Commission')</th>
                                    <th>@lang('Net Payout')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settlements as $item)
                                    @php
                                        $gross = $item->price * $item->ticket_count;
                                        $comm = $gross * ($commissionRate / 100);
                                        $net = $gross - $comm;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $item->trip->title }}</span> <br>
                                            <small>{{ showDateTime($item->date_of_journey, 'M d, Y') }}</small>
                                        </td>
                                        <td>{{ gs('cur_sym') }}{{ getAmount($gross) }}</td>
                                        <td class="text--danger">-{{ gs('cur_sym') }}{{ getAmount($comm) }}</td>
                                        <td class="fw-bold text--success">{{ gs('cur_sym') }}{{ getAmount($net) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No settlements found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($settlements->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($settlements) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
