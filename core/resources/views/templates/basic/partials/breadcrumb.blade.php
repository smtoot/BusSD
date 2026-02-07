@php
    $breadcrumbContent = @getContent('breadcrumb.content', true);
@endphp
<section class="inner-banner-section banner-section bg-overlay-primary bg_img"
    data-background="{{ frontendImage('breadcrumb', @$breadcrumbContent->data_values->image, '1915x365') }}">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-12 text-center">
                <div class="banner-content">
                    <h2 class="title">
                        @lang($pageTitle)
                    </h2>
                </div>
            </div>
        </div>
    </div>
</section>
