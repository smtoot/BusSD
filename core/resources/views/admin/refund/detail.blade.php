@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center gy-4">
        {{-- Refund Information --}}
        <div class="col-xl-4 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Refund Details')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('TRX')
                            <span class="fw-bold">{{ $refund->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Refund Amount')
                            <span class="fw-bold">{{ showAmount($refund->amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($refund->status == 0)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @elseif($refund->status == 1)
                                <span class="badge badge--success">@lang('Approved')</span>
                            @elseif($refund->status == 2)
                                <span class="badge badge--danger">@lang('Rejected')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Requested At')
                            <span>{{ showDateTime($refund->created_at) }}</span>
                        </li>
                        @if($refund->admin_feedback)
                            <li class="list-group-item">
                                <span class="text-black">@lang('Admin Feedback')</span>
                                <p class="mt-1">{{ __($refund->admin_feedback) }}</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Passenger Information --}}
            <div class="card overflow-hidden box--shadow1 mt-4">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Passenger Information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">{{ @$refund->passenger->firstname }} {{ @$refund->passenger->lastname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span>{{ @$refund->passenger->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span>{{ @$refund->passenger->mobile }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Booking Information --}}
        <div class="col-xl-8 col-md-6">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Booking Information')</h5>
                    @if($refund->bookedTicket)
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('Booking PNR')
                                        <span class="fw-bold">{{ @$refund->bookedTicket->trx }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('Trip')
                                        <span class="fw-bold">{{ @$refund->bookedTicket->trip->title ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('Journey Date')
                                        <span>{{ @$refund->bookedTicket->date_of_journey }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('Seats')
                                        <span class="fw-bold">
                                            @if(is_array(@$refund->bookedTicket->seats))
                                                {{ implode(', ', @$refund->bookedTicket->seats) }}
                                            @else
                                                {{ @$refund->bookedTicket->seats }}
                                            @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('Booking Amount')
                                        <span class="fw-bold">{{ showAmount(@$refund->bookedTicket->price) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('Operator')
                                        @if(@$refund->bookedTicket->trip->owner)
                                            <a href="{{ route('admin.users.detail', @$refund->bookedTicket->trip->owner->id) }}">
                                                <span>@</span>{{ @$refund->bookedTicket->trip->owner->username }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mt-3">@lang('Booking information not available')</p>
                    @endif

                    @if($refund->status == 0)
                        <div class="row mt-4">
                            <div class="col-md-12 border-top pt-3">
                                <button class="btn btn-outline--success btn-sm ms-1 confirmationBtn"
                                    data-action="{{ route('admin.refund.approve') }}"
                                    data-question="@lang('Are you sure to approve this refund? The operator\'s balance will be debited.')"
                                    data-hidden_fields='{"id":"{{ $refund->id }}"}'
                                >
                                    <i class="las la-check"></i> @lang('Approve Refund')
                                </button>

                                <button class="btn btn-outline--danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="las la-ban"></i> @lang('Reject Refund')
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    @if($refund->status == 0)
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Refund Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.refund.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $refund->id }}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to reject the refund of')
                            <span class="fw-bold text--success">{{ showAmount($refund->amount) }}</span>
                            @lang('for') <span class="fw-bold">{{ @$refund->passenger->firstname }} {{ @$refund->passenger->lastname }}</span>?
                        </p>
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
