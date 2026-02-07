@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        @foreach ($packages as $package)
            <div class="col-xxl-3 col-lg-4 col-md-6 mb-30">
                <div class="card">
                    <div class="card-body">
                        <div class="pricing-table text-center">
                            <h4 class="package-name b-radius--capsule bg--10 mb-20 p-2">{{ __($package->name) }}</h4>
                            <span class="price">{{ showAmount($package->price) }}</span>
                            <p>@lang('For') {{ getPackageLimitUnit($package->time_limit, $package->unit) }}</p>
                            <ul class="package-features-list mt-50">
                                @foreach ($features as $item)
                                    <li><i class="fas fa-check-circle text--success"></i> {{ __($item->name) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--success h-45 buyPack w-100"
                            data-action="{{ route('owner.package.buy', $package->id) }}"
                            data-name="{{ __($package->name) }}" data-price="{{ showAmount($package->price) }}" ;
                            data-expires="{{ getPackageExpiryDate($package->time_limit, $package->unit) }}">
                            @lang('Buy')
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="buyPackModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buyPackModalLabel">@lang('Buy Package Preview')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post">
                    @csrf
                    <div class="modal-body">
                        <ul class="list-group d-flex list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>@lang('Name') </strong> <span class="package-name"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>@lang('Price')</strong> <span class="package-price"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function() {
            "use strict";

            $('.buyPack').on('click', function() {
                var modal = $('#buyPackModal');

                modal.find('.package-name').text($(this).data('name'));
                modal.find('.package-price').text($(this).data('price'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
