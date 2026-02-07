@php
    $serviceContent = getContent('service.content', true);
    $serviceElements = getContent('service.element', orderById: true);
@endphp
<section class="choose-section ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-header">
                    <h2 class="section-title" data-s-break>
                        {{ __(@$serviceContent->data_values->heading) }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center ml-b-40">
            @foreach ($serviceElements ?? [] as $serviceElement)
                <div class="col-lg-4 col-md-6 col-sm-8 mrb-30">
                    <div class="choose-item d-flex flex-wrap">
                        <div class="choose-icon">
                            @php echo @$serviceElement->data_values->icon @endphp
                        </div>
                        <div class="choose-content">
                            <h3 class="title">{{ __(@$serviceElement->data_values->title) }}</h3>
                            <p>{{ __(@$serviceElement->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
