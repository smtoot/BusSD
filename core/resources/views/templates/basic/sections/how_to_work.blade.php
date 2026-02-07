@php
    $howToWorkContent = getContent('how_to_work.content', true);
    $howToWorkElements = getContent('how_to_work.element', orderById: true);
@endphp
<section class="process-section ptb-80 bg-overlay-primary-two bg_img"
    data-background="{{ frontendImage('how_to_work', @$howToWorkContent->data_values->image, '1920x435') }}"
    id="process">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-header">
                    <h2 class="section-title" data-s-break>{{ __(@$howToWorkContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="process-area">
            <div class="row justify-content-center ml-b-30">
                @foreach (@$howToWorkElements ?? [] as $howToWorkElement)
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mrb-30">
                        <div class="process-item text-center">
                            <div class="process-icon">
                                @php echo $howToWorkElement->data_values->icon @endphp
                            </div>
                            <div class="process-content">
                                <h3 class="title">{{ __($howToWorkElement->data_values->title) }}</h3>
                                <span class="sub-title">@lang('Step') {{ $loop->iteration }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
