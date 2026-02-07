@extends('Template::layouts.app')

@section('main-content')
    @include('Template::partials.preloader')
    @include('Template::partials.header')

    @if (!request()->routeIs('home'))
        @include('Template::partials.breadcrumb')
    @endif

    @yield('content')

    @include('Template::partials.footer')

    <a href="#" class="scrollToTop">
        <i class="fa fa-angle-up"></i>
    </a>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            let elements = document.querySelectorAll('[data-s-break]');
            Array.from(elements).forEach(element => {
                let html = element.innerText;
                if (typeof html != 'string') {
                    return false;
                }
                html = html.split(' ');
                let lastValue = html.pop();
                let colorText = `<span class="text--base">${lastValue}</span>`;
                html.push(colorText);
                html = html.join(" ");
                element.innerHTML = html;
            });
        })(jQuery)
    </script>
@endpush
