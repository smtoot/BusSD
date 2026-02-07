@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Withdraw Money')</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="las la-info-circle"></i> @lang('Your Current Balance'): <strong>{{ gs('cur_sym') }}{{ getAmount(authUser()->balance) }}</strong>
                    </div>

                    @if($methods->isEmpty())
                        <div class="alert alert-warning text-center">
                            <i class="las la-exclamation-triangle"></i>
                            @lang('No withdrawal methods available at the moment. Please contact support.')
                        </div>
                    @else
                        <form action="{{ route('owner.withdraw.money') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>@lang('Withdrawal Method')</label>
                                <select name="method_code" class="form-control" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach($methods as $method)
                                        <option value="{{ $method->id }}">{{ __($method->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('Amount')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input type="number" step="any" name="amount" class="form-control" placeholder="0.00" required>
                                </div>
                                <small class="form-text text-muted">
                                    @lang('Withdrawal limits will be shown after selecting a method')
                                </small>
                            </div>

                            <button type="submit" class="btn btn--primary w-100">
                                <i class="las la-paper-plane"></i> @lang('Submit Withdrawal Request')
                            </button>
                        </form>

                        <div class="mt-4">
                            <h6>@lang('Available Methods')</h6>
                            <div class="row">
                                @foreach($methods as $method)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6>{{ __($method->name) }}</h6>
                                                <p class="mb-1"><small>@lang('Min'): {{ gs('cur_sym') }}{{ getAmount($method->min_limit) }}</small></p>
                                                <p class="mb-1"><small>@lang('Max'): {{ gs('cur_sym') }}{{ getAmount($method->max_limit) }}</small></p>
                                                <p class="mb-0">
                                                    <small class="text-muted">
                                                        @lang('Charge'): {{ getAmount($method->fixed_charge) }} {{ gs('cur_text') }}
                                                        @if($method->percent_charge > 0)
                                                            + {{ getAmount($method->percent_charge) }}%
                                                        @endif
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
