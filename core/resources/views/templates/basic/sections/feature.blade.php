@php
    $featureContent = getContent('feature.content', true);
    $featureElements = getContent('feature.element', orderById: true);
@endphp

<section class="feature-section ptb-80" id="feature">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-header">
                    <h2 class="section-title" data-s-break>{{ __(@$featureContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center ml-b-30">
            @foreach ($featureElements ?? [] as $featureElement)
                <div class="col-lg-4 col-md-6 col-sm-8 mrb-30">
                    <div class="feature-item text-center">
                        <div class="feature-icon">
                            @php
                                echo @$featureElement->data_values->icon;
                            @endphp
                        </div>
                        <div class="feature-content">
                            <h3 class="title">{{ __($featureElement->data_values->title) }}</h3>
                            <p>{{ __($featureElement->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
