@foreach ($packages ?? [] as $package)
    <div class="col-lg-4 col-md-6 col-sm-8 mrb-30">
        <div class="pricing-item">
            <div class="pricing-header text-center">
                <h3 class="sub-title">
                    <span>{{ __($package->name) }}</span>
                </h3>
                <h2 class="title">
                    {{ gs('cur_sym') }}{{ showAmount($package->price, currencyFormat: false) }}
                    <span class="pricing-post">
                        / {{ getPackageLimitUnit($package->time_limit, $package->unit) }}
                    </span>
                </h2>
                <div class="pricing-shape">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="pricing-body text-center">
                <ul class="pricing-list">
                    @foreach ($planFeatures as $item)
                        <li>@lang($item->name) </li>
                    @endforeach
                </ul>
            </div>
            <div class="pricing-btn-area text-center">
                <a href="@if (auth()->guard('owner')->check()) {{ route('owner.package.index') }} @else {{ route('owner.login') }} @endif"
                    class="cmn-btn-active">@lang('Subscribe Now!')
                    <span></span>
                    <span></span>
                    <span></span>
                </a>
            </div>
        </div>
    </div>
@endforeach
