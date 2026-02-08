@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center gy-4">
        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Withdrawal Details')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="fw-bold">{{ showDateTime($withdrawal->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Transaction Number')
                            <span class="fw-bold">{{ $withdrawal->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Operator')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $withdrawal->owner_id) }}"><span>@</span>{{ @$withdrawal->owner->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="fw-bold">{{ @$withdrawal->method->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="fw-bold">{{ showAmount($withdrawal->amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Charge')
                            <span class="fw-bold text--danger">{{ showAmount($withdrawal->charge) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Charge')
                            <span class="fw-bold">{{ showAmount($withdrawal->after_charge) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Final Amount')
                            <span class="fw-bold">{{ showAmount($withdrawal->final_amount, currencyFormat: false) }} {{ $withdrawal->currency }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($withdrawal->status == 0)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @elseif($withdrawal->status == 1)
                                <span class="badge badge--success">@lang('Approved')</span>
                            @elseif($withdrawal->status == 2)
                                <span class="badge badge--danger">@lang('Rejected')</span>
                            @endif
                        </li>
                        @if($withdrawal->admin_feedback)
                            <li class="list-group-item">
                                <span class="text-black">@lang('Admin Feedback')</span>
                                <p class="mt-1">{{ __($withdrawal->admin_feedback) }}</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Withdrawal Information')</h5>

                    @if($withdrawal->withdraw_information)
                        @php $info = is_string($withdrawal->withdraw_information) ? json_decode($withdrawal->withdraw_information, true) : $withdrawal->withdraw_information; @endphp
                        @if(is_array($info))
                            @foreach($info as $key => $value)
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <h6>{{ __(ucfirst(str_replace('_', ' ', $key))) }}</h6>
                                    </div>
                                    <div class="col-md-8">
                                        <p>{{ $value }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @else
                        <p class="text-muted mt-3">@lang('No additional information provided')</p>
                    @endif

                    @if($withdrawal->status == 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn btn-outline--success btn-sm ms-1 confirmationBtn"
                                    data-action="{{ route('admin.withdraw.approve') }}"
                                    data-question="@lang('Are you sure to approve this withdrawal?')"
                                    data-hidden_fields='{"id":"{{ $withdrawal->id }}"}'
                                >
                                    <i class="las la-check"></i> @lang('Approve')
                                </button>

                                <button class="btn btn-outline--danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="las la-ban"></i> @lang('Reject')
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    @if($withdrawal->status == 0)
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Withdrawal Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.withdraw.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $withdrawal->id }}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span>
                            <span class="fw-bold text--success">{{ showAmount($withdrawal->amount) }}</span>
                            @lang('withdrawal of') <span class="fw-bold">{{ @$withdrawal->owner->username }}</span>?
                        </p>
                        <p class="text--info mt-2">@lang('The amount will be refunded to the operator\'s balance.')</p>
                        <div class="form-group">
                            <label class="mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="details" maxlength="255" class="form-control" rows="5" required>{{ old('details') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ url()->previous() }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back')
    </a>
@endpush
