<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                        <a class="site-logo site-title" href="{{ route('home') }}">
                            <img src="{{ siteLogo() }}" alt="site-logo">
                        </a>
                        <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ml-auto mr-auto">
                                <li>
                                    <a href="{{ route('home') }}">@lang('Home')</a>
                                </li>
                                <li>
                                    <a
                                        href="{{ request()->routeIs('home') ? '#feature' : route('home') . '#feature' }}">
                                        @lang('Features')
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ request()->routeIs('home') ? '#process' : route('home') . '#process' }}">
                                        @lang('Process')
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ request()->routeIs('home') ? '#package' : route('home') . '#package' }}">
                                        @lang('Package')
                                    </a>
                                </li>
                            </ul>
                            <div class="header-action">
                                @if (gs('multi_language'))
                                    @php
                                        $language = App\Models\Language::all();
                                        $selectedLang = $language->where('code', session('lang'))->first();
                                    @endphp
                                    <div class="dropdown-lang dropdown mt-0 d-block">
                                        <a href="#" class="language-btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <img class="flag"
                                                src="{{ getImage(getFilePath('language') . '/' . @$selectedLang->image, getFileSize('language')) }}"
                                                alt="us">
                                            <span class="language-text text-white">{{ @$selectedLang->name }}</span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            @foreach ($language as $lang)
                                                <li><a href="{{ route('lang', $lang->code) }}"><img class="flag"
                                                            src="{{ getImage(getFilePath('language') . '/' . @$lang->image, getFileSize('language')) }}"
                                                            alt="@lang('image')">
                                                        {{ @$lang->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (!auth()->guard('owner')->check())
                                    <a href="{{ route('owner.login') }}" class="cmn-btn">@lang('Owner Login')
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </a>
                                    @if (gs('registration'))
                                        <a href="{{ route('owner.register') }}" class="cmn-btn">@lang('Register')
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </a>
                                    @endif
                                @endif
                                @if (auth()->guard('owner')->check())
                                    <a href="{{ route('owner.dashboard') }}" class="cmn-btn">@lang('Owner Panel')</a>
                                @endif
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
