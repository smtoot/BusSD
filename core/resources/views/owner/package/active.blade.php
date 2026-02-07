@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        @if ($soldPackage)
            <div class="col-xxl-3 col-lg-4 col-md-6 mb-30">
                <div class="card">
                    <div class="card-body">
                        <div class="pricing-table text-center">
                            <h4 class="package-name b-radius--capsule bg--10 mb-20 p-2">
                                {{ __($soldPackage->package->name) }}
                            </h4>
                            <span class="price">{{ showAmount($soldPackage->price) }}</span>
                            <small>
                                {{ getPackageLimitUnit($soldPackage->time_limit, $soldPackage->unit) }}
                            </small>
                            <p class="font-weight-bold text--danger">
                                @lang('Expired On'):
                                {{ showDateTime($soldPackage->ends_at, 'd F, Y') }}
                            </p>
                            <ul class="package-features-list mt-5">
                                @foreach ($features as $item)
                                    <li><i class="fas fa-check-circle text--success"></i>{{ __($item->name) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-5">
                        <h6 class="text-center text--danger">
                            @lang('You don\'t have any active package')
                        </h6>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
