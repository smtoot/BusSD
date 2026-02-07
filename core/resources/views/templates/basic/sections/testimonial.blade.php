@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonialElements = getContent('testimonial.element', orderById: true);
@endphp
<div class="client-section ptb-80 bg-overlay-primary-two bg_img"
    data-background="{{ frontendImage('testimonial', @$testimonialContent->data_values->image, '1920x405') }}">
    <div class="container">
        <div class="client-area">
            <div class="row justify-content-center align-items-end ml-b-20">
                <div class="col-lg-12 text-center">
                    <div class="section-header">
                        <h2 class="section-title">{{ __(@$testimonialContent->data_values->heading) }}</h2>
                    </div>
                    <div class="client-slider">
                        <div class="swiper-wrapper">
                            @foreach ($testimonialElements ?? [] as $testimonialElement)
                                <div class="swiper-slide">
                                    <div class="client-content text-center">
                                        <p>{{ __($testimonialElement->data_values->quote) }}</p>
                                        <h4 class="text-white mt-4">{{ __($testimonialElement->data_values->author) }}
                                        </h4>
                                        <h6 class="text-white font-italic font-weight-normal">
                                            {{ __(@$testimonialElement->data_values->designation) }}</h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
