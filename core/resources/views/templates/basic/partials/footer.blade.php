@php
    $footerContent = getContent('footer.content', true);
    $policyPages = getContent('policy_pages.element', orderById: true);
    $socialIcons = getContent('social_icon.element');
@endphp
<footer class="footer-section ptb-80 bg-overlay-primary-two bg_img"
    data-background="{{ frontendImage('footer', @$footerContent->data_values->image, '1915x400') }}">
    <div class="container">
        <div class="footer-area">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a class="site-logo site-title" href="{{ route('home') }}">
                                <img src="{{ siteLogo() }}" alt="site-logo">
                            </a>
                        </div>
                        <p>{{ __(@$footerContent->data_values->description) }}</p>
                        <div class="social-area">
                            <ul class="footer-social">
                                @foreach ($socialIcons ?? [] as $socialIcon)
                                    <li>
                                        <a href="{{ $socialIcon->data_values->url }}" target="_blank">
                                            @php echo $socialIcon->data_values->social_icon @endphp
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="footer-widget useful-links">
                            <ul>
                                <li><a href="{{ route('owner.login') }}">@lang('Owner Login')</a></li>
                                <li><a href="{{ route('co-owner.login') }}">@lang('Login As Co-Owner')</a></li>
                                <li><a href="{{ route('manager.login') }}">@lang('Login As Counter Manager')</a></li>
                                <li><a href="{{ route('driver.login') }}">@lang('Login As Driver')</a></li>
                                <li><a href="{{ route('supervisor.login') }}">@lang('Login As Supervisor')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="privacy-area">
    <div class="container">
        <div class="copyright-area d-flex flex-wrap align-items-center justify-content-between">
            <div class="copyright">
                <p>
                    @lang('Copyright')©{{ now()->year }}
                    <a class="text--base" href="{{ route('home') }}">{{ gs('site_name') }}</a>.
                    @lang('All Rights Reserved')
                </p>
            </div>
            <ul class="copyright-list">
                @foreach ($policyPages ?? [] as $policyPage)
                    <li>
                        <a href="{{ route('policy.pages', $policyPage->slug) }}">
                            {{ __(@$policyPage->data_values->title) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
