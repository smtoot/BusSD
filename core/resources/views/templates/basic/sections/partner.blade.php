 @php
     $partnerElements = getContent('partner.element', orderById: true);
 @endphp
 <div class="brand-section ptb-80">
     <div class="container">
         <div class="row">
             <div class="col-lg-12">
                 <div class="brand-wrapper">
                     <div class="swiper-wrapper">
                         @foreach ($partnerElements ?? [] as $partnerElement)
                             <div class="swiper-slide">
                                 <div class="BrandSlider">
                                     <div class="brand-item">
                                         <img src="{{ frontendImage('partner', $partnerElement->data_values->image, '150x60') }}"
                                             alt="partner images">
                                     </div>
                                 </div>
                             </div>
                         @endforeach
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
