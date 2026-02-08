@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($method) ? route('admin.withdraw.method.update', $method->id) : route('admin.withdraw.method.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Method Name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', @$method->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    @if(isset($method) && $method->image)
                                        <small class="text-muted">@lang('Current image will be replaced if a new one is uploaded')</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Minimum Limit')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="min_limit" class="form-control" value="{{ old('min_limit', @$method->min_limit ? getAmount(@$method->min_limit) : '') }}" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Maximum Limit')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="max_limit" class="form-control" value="{{ old('max_limit', @$method->max_limit ? getAmount(@$method->max_limit) : '') }}" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Fixed Charge')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="fixed_charge" class="form-control" value="{{ old('fixed_charge', @$method->fixed_charge ? getAmount(@$method->fixed_charge) : 0) }}" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Percent Charge')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="percent_charge" class="form-control" value="{{ old('percent_charge', @$method->percent_charge ? getAmount(@$method->percent_charge) : 0) }}" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Processing Time')</label>
                                    <input type="text" name="delay" class="form-control" value="{{ old('delay', @$method->delay) }}" placeholder="@lang('e.g. 24-48 hours')" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', @$method->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45 mt-3">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.withdraw.method.index') }}" class="btn btn-sm btn--dark">
        <i class="las la-arrow-left"></i> @lang('Back to Methods')
    </a>
@endpush
