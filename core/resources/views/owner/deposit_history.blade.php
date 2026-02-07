@extends('owner.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Gateway | Trx')</th>
                                <th>@lang('Initiated')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Conversion')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Details')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                                <tr>
                                    <td>
                                        <span class="fw-bold">
                                            <span class="text-primary">
                                                @if($deposit->method_code < 5000)
                                                    {{ __(@$deposit->gateway->name) }}
                                                @else
                                                    @lang('Google Pay')
                                                @endif
                                            </span>
                                        </span>
                                        <br>
                                        <small> {{ $deposit->trx }} </small>4
                                    </td>
                                    <td>
                                        {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                    </td>
                                    <td>
                                        {{ showAmount($deposit->amount ) }} + <span class="text--danger" data-bs-toggle="tooltip" title="@lang('Processing Charge')">{{ showAmount($deposit->charge)}} </span>
                                        <br>
                                        <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->amount+$deposit->charge) }}
                                        </strong>
                                    </td>
                                    <td>
                                        {{ showAmount(1) }}  =  {{ showAmount($deposit->rate,currencyFormat:false) }} {{__($deposit->method_currency)}}
                                        <br>
                                        <strong>{{ showAmount($deposit->final_amount,currencyFormat:false) }} {{__($deposit->method_currency)}}</strong>
                                    </td>
                                    <td>
                                        @php echo $deposit->statusBadge @endphp
                                    </td>
                                    @php
                                        $details = [];
                                        if($deposit->method_code >= 1000 && $deposit->method_code <= 5000){
                                            foreach (@$deposit->detail ?? [] as $key => $info) {
                                                $details[] = $info;
                                                if ($info->type == 'file') {
                                                    $details[$key]->value = route('owner.download.attachment', encrypt(getFilePath('verify').'/'.$info->value));
                                                }
                                            }
                                        }
                                    @endphp
                                    <td>
                                        <div class="button--group">
                                            @if($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                                <button 
                                                    type="button"
                                                    class="btn btn-sm btn-outline--primary detailBtn" 
                                                    data-info="{{ json_encode($details) }}"
                                                    @if ($deposit->status == Status::PAYMENT_REJECT)
                                                        data-admin_feedback="{{ $deposit->admin_feedback }}"
                                                    @endif
                                                >
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </button>
                                            @else
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline--success" 
                                                    data-bs-toggle="tooltip" 
                                                    title="@lang('Automatically processed')"
                                                >
                                                    <i class="las la-desktop"></i> @lang('Details')
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
                    </table><!-- table end -->
                </div>
            </div>
            @if ($deposits->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($deposits) }}
                </div>
            @endif
        </div>
    </div>
</div>

<div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Details')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush userData mb-2">
                </ul>
                <div class="feedback"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.detailBtn').on('click', function () {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if(userData){
                    userData.forEach(element => {
                        if(element.type != 'file'){
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        }else{
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if($(this).data('admin_feedback') != undefined){
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                }else{
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush