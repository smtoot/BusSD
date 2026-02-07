@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('owner.deposit.insert') }}" method="post" class="deposit-form">
                @csrf
                <input type="hidden" name="currency">
                <div class="gateway-card">
                    <div class="row justify-content-center gy-sm-4 gy-3">
                        <div class="col-lg-6">
                            <div class="payment-system-list is-scrollable gateway-option-list">
                                @foreach ($gatewayCurrency as $data)
                                    <label for="{{ titleToKey($data->name) }}"
                                        class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                        <div class="payment-item__info">
                                            <span class="payment-item__check"></span>
                                            <span class="payment-item__name">{{ __($data->name) }}</span>
                                        </div>
                                        <div class="payment-item__thumb">
                                            <img class="payment-item__thumb-img"
                                                src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                alt="@lang('payment-thumb')">
                                        </div>
                                        <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}"
                                            hidden data-gateway='@json($data)' type="radio" name="gateway"
                                            value="{{ $data->method_code }}" @checked(old('gateway', $loop->first) == $data->method_code)>
                                    </label>
                                @endforeach
                                @if ($gatewayCurrency->count() > 4)
                                    <button type="button" class="payment-item__btn more-gateway-option">
                                        <p class="payment-item__btn-text text-white">@lang('Show All Payment Options')</p>
                                        <span class="payment-item__btn__icon text-white"><i
                                                class="fas fa-chevron-down"></i></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="payment-system-list p-3">
                                <div class="deposit-info">
                                    <div class="deposit-info__title">
                                        <p class="text mb-0">@lang('Amount')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <div class="deposit-info__input-group input-group">
                                            <span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>
                                            <input type="text" class="form-control amount" name="amount"
                                                placeholder="@lang('00.00')" value="{{ getAmount($order->price) }}"
                                                autocomplete="off" readonly>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="deposit-info">
                                    <div class="deposit-info__title">
                                        <p class="text has-icon">@lang('Processing Charge')
                                            <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')"
                                                class="proccessing-fee-info"><i class="las la-info-circle"></i> </span>
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="processing-fee">@lang('0.00')</span>
                                            {{ __(gs('cur_text')) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="deposit-info total-amount pt-3">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('Total')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="final-amount">@lang('0.00')</span>
                                            {{ __(gs('cur_text')) }}</p>
                                    </div>
                                </div>

                                <div class="deposit-info gateway-conversion d-none total-amount pt-2">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('Conversion')
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"></p>
                                    </div>
                                </div>
                                <div class="deposit-info conversion-currency d-none total-amount pt-2">
                                    <div class="deposit-info__title">
                                        <p class="text">
                                            @lang('In') <span class="gateway-currency"></span>
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text">
                                            <span class="in-currency"></span>
                                        </p>

                                    </div>
                                </div>
                                <div class="d-none crypto-message mb-3">
                                    @lang('Conversion with')
                                    <span class="gateway-currency"></span>
                                    @lang('and final value will Show on next step')
                                </div>
                                <button type="submit" class="btn btn--primary w-100 h-45 mt-1" disabled>
                                    @lang('Confirm Payment')
                                </button>
                                <div class="info-text pt-3">
                                    <p class="text">@lang('Ensuring your funds grow safely through our secure payment process with world-class payment options.')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .gateway-card {
            padding: 15px;
        }

        .payment-card-title {
            padding: 13px 25px;
            text-align: center;
            background-color: #4634ff;
            border-radius: 5px;
            border: 0;
            margin-bottom: 0px;
            color: #fff;
        }

        .payment-system-list {
            --thumb-width: 100px;
            --thumb-height: 40px;
            --radio-size: 12px;
            --border-color: #cccccf59;
            --hover-border-color: #4634ff;
            background-color: #fff;
            border-radius: 5px;
            height: 100%;

        }

        .payment-system-list.is-scrollable {
            max-height: min(388px, 70vh);
            overflow-x: auto;
            padding-block: 4px;
        }

        .payment-system-list.is-scrollable::-webkit-scrollbar {
            width: 5px;
        }

        .payment-system-list.is-scrollable::-webkit-scrollbar {
            width: 5px;

        }

        .payment-system-list.is-scrollable::-webkit-scrollbar-thumb {
            background-color: #4634ff;
            border-radius: 10px;
        }

        .payment-item {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding: 10px 18px;
            border: 1px solid #fff;
            border-top-color: var(--border-color);
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .payment-item:first-child {
            border-top-color: #fff;
            border-radius: 5px 5px 0 0;
        }

        .payment-item:has(.payment-item__radio:checked) {
            border-left: 3px solid var(--hover-border-color);
            border-radius: 0px;
        }

        .payment-item__check {
            border: 3px solid transparent;
        }

        .payment-item:has(.payment-item__radio:checked) .payment-item__check {
            border: 3px solid #4634ff;
        }

        .payment-item__info {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            max-width: calc(100% - var(--thumb-width))
        }

        .payment-item__check {
            width: var(--radio-size);
            height: var(--radio-size);
            border: 1px solid #4634ff;
            display: inline-block;
            border-radius: 100%;

        }

        .payment-item__name {
            padding-left: 10px;
            width: calc(100% - var(--radio-size));
            transition: all 0.3s;
        }

        .payment-item__thumb {
            width: var(--thumb-width);
            height: var(--thumb-height);
            text-align: right;
            padding-left: 10px;

            &:has(.text) {
                width: fit-content;
            }
        }

        .payment-item__thumb img {
            max-width: var(--thumb-width);
            max-height: var(--thumb-height);
            object-fit: cover;
        }


        .deposit-info {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .deposit-info__title {
            max-width: 50%;
            margin-top: 5px;
            margin-bottom: 15px;
            text-align: left;
        }

        .deposit-info__input {
            max-width: 50%;
            text-align: right;
            width: 100%;
            margin-bottom: 15px;
            margin-top: 5px;
        }

        .deposit-info__input-select {
            border: 1px solid var(--border-color);
            width: 100%;
            border-radius: 5px;
            padding-block: 6px;
        }

        .deposit-info__input-group {
            border: 1px solid #ededee;
            border-radius: 5px;

            .deposit-info__input-group-text {
                align-self: center;
                padding-left: 10px;
                padding-right: 10px;
            }

        }

        .deposit-info__input-group .form--control {
            padding: 5px;
            border: 0;
            height: 35px;
            text-align: right;
        }

        .deposit-info__input-group .form--control:focus {
            box-shadow: unset;
        }

        .info-text .text,
        .deposit-info__input .text {
            font-size: 14px;

        }

        .deposit-info__title .text.has-icon {
            display: flex;
            align-items: center;
            gap: 5px
        }

        .total-amount {
            border-top: 1px solid var(--border-color);
        }

        .total-amount .deposit-info__title {
            font-weight: 600;
        }

        .payment-item__btn {
            border: 0;
            border-block: 1px solid var(--border-color);
            border-bottom: 0;
            background: #4634ff;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 15px;
            font-weight: 500;
        }

        .payment-item:hover+.payment-item__btn {
            border-top-color: #fff;
        }

        .deposit-info__input-group .form-control {
            text-align: end;
            border-left: 1px solid #ededee;
        }

        hr {
            color: #ededee;
            opacity: 1 !important;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            var amount = parseFloat({{ getAmount($order->price) }});
            var gateway;

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                calculation();
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')
                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
                        8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush
