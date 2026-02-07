@php
    $packageContent = getContent('package.content', true);
    $packages = App\Models\Package::active()->orderBy('price')->get();
    $planFeatures = App\Models\Feature::orderByDesc('id')->get();
@endphp
<section class="pricing-section ptb-80" id="package">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-header">
                    <h2 class="section-title" data-s-break>{{ __(@$packageContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center ml-b-30">
            @include('Template::partials.package_card')
        </div>
    </div>
</section>
