@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Withdrawal History')</h5>
                    <a href="{{ route('owner.withdraw.methods') }}" class="btn btn--primary btn-sm">
                        <i class="las la-plus"></i> @lang('New Withdrawal')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Method')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Receivable')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdraws as $withdraw)
                                    <tr>
                                        <td>{{ showDateTime($withdraw->created_at, 'M d, Y') }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $withdraw->trx }}</span>
                                        </td>
                                        <td>{{ __($withdraw->method->name) }}</td>
                                        <td class="fw-bold">{{ gs('cur_sym') }}{{ getAmount($withdraw->amount) }}</td>
                                        <td class="text--danger">{{ gs('cur_sym') }}{{ getAmount($withdraw->charge) }}</td>
                                        <td class="text--success fw-bold">{{ gs('cur_sym') }}{{ getAmount($withdraw->after_charge) }}</td>
                                        <td>
                                            @if($withdraw->status == 0)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($withdraw->status == 1)
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($withdraw->status == 2)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No withdrawals found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($withdraws->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($withdraws) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
