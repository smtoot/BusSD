@php
    $bannerContent = getContent('banner.content', true);
@endphp
<section class="banner-section bg-overlay-primary bg_img"
    data-background="{{ frontendImage('banner', @$bannerContent->data_values->image, '1915x945') }}">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-12 text-center">
                <div class="banner-content">
                    <h2 class="title">
                        {{ __(@$bannerContent->data_values->heading) }}
                    </h2>
                    <h3 class="sub-title">{{ __(@$bannerContent->data_values->subheading) }}</h3>
                    <div class="banner-btn">
                        <a href="{{ @$bannerContent->data_values->button_one_link }}" class="cmn-btn">
                            {{ __(@$bannerContent->data_values->button_one) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
