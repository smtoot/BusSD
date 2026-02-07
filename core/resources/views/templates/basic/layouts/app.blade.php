<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(isRTL()) dir="rtl" @endif itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title> {{ gs()->siteName(__($pageTitle)) }}</title>

    @include('partials.seo')

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">


    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/themify.css') }}">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/animate.css') }}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/style.css') }}">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/custom.css') }}">

    <link rel="stylesheet"
        href="{{ asset(activeTemplate(true) . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}">

    @stack('style')
</head>

@php echo loadExtension('google-analytics') @endphp

<body>
    @yield('main-content')

    @include('Template::partials.cookie')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset(activeTemplate(true) . '/js/jquery-migrate-3.0.0.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . '/js/jquery.nice-select.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . '/js/swiper.min.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . '/js/plugin.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . '/js/wow.min.js') }}"></script>

    @stack('script-lib')

    <script src="{{ asset(activeTemplate(true) . '/js/main.js') }}"></script>

    @php echo loadExtension('tawk-chat') @endphp

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        (function($) {
            "use strict";

            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });
        })(jQuery);
    </script>
</body>

</html>
