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
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Method')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('After Charge')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Requested At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $withdrawal)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$withdrawal->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', @$withdrawal->owner_id) }}"><span>@</span>{{ @$withdrawal->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$withdrawal->method->name ?? 'N/A' }}</span>
                                            <br>
                                            <small>{{ $withdrawal->trx }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($withdrawal->amount) }}</span>
                                        </td>
                                        <td>
                                            <span class="text--danger">{{ showAmount($withdrawal->charge) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($withdrawal->after_charge) }}</span>
                                        </td>
                                        <td>
                                            @if($withdrawal->status == 0)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($withdrawal->status == 1)
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($withdrawal->status == 2)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($withdrawal->created_at) }}
                                            <br>
                                            {{ diffForHumans($withdrawal->created_at) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.withdraw.details', $withdrawal->id) }}" class="btn btn-sm btn-outline--primary ms-1">
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
                @if($withdrawals->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($withdrawals) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder='Username / TRX' />
@endpush
