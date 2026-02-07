@php
    $inviteContent = getContent('invite.content', true);
@endphp
<section class="call-to-action-section call-to-action-section-two pd-t-60 pd-b-60">
    <div class="container">
        <div class="row justify-content-between align-items-center ml-b-30">
            <div class="col-lg-8 mrb-30">
                <div class="call-to-action-content">
                    <h3 class="title">{{ __(@$inviteContent->data_values->text) }}</h3>
                </div>
            </div>
            <div class="col-lg-4 mrb-30">
                <div class="call-to-action-btn">
                    <a href="{{ @$inviteContent->data_values->button_link }}" class="cmn-btn">
                        {{ __(@$inviteContent->data_values->button_name) }}
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
