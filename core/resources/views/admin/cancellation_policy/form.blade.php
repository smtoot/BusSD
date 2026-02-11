@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ isset($policy) ? route('admin.cancellation.policy.update', $policy->id) : route('admin.cancellation.policy.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Policy Name (Key)')</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="{{ old('name', @$policy->name) }}" 
                                           {{ isset($policy) && $policy->is_system ? 'readonly' : 'required' }}>
                                    <small class="text-muted">@lang('Unique identifier (e.g., flexible, strict)')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Display Label')</label>
                                    <input type="text" class="form-control" name="label" 
                                           value="{{ old('label', @$policy->label) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Description')</label>
                            <textarea class="form-control" name="description" rows="2">{{ old('description', @$policy->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>@lang('Refund Rules')</span>
                                <button type="button" class="btn btn-sm btn-outline--success" id="addRuleBtn">
                                    <i class="la la-plus"></i>@lang('Add Rule')
                                </button>
                            </label>
                            <div id="rulesContainer">
                                @if(isset($policy) && $policy->rules)
                                    @foreach($policy->rules as $index => $rule)
                                        <div class="rule-row mb-2">
                                            <div class="input-group">
                                                <span class="input-group-text">@lang('If cancelled')</span>
                                                <input type="number" class="form-control" name="rules[{{ $index }}][hours_before]" 
                                                       placeholder="Hours before" value="{{ $rule['hours_before'] }}" required min="0">
                                                <span class="input-group-text">@lang('hours before, refund')</span>
                                                <input type="number" class="form-control" name="rules[{{ $index }}][refund_percentage]" 
                                                       placeholder="%" value="{{ $rule['refund_percentage'] }}" required min="0" max="100">
                                                <span class="input-group-text">%</span>
                                                <button type="button" class="btn btn-outline--danger removeRuleBtn">
                                                    <i class="la la-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="rule-row mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text">@lang('If cancelled')</span>
                                            <input type="number" class="form-control" name="rules[0][hours_before]" 
                                                   placeholder="Hours before" value="24" required min="0">
                                            <span class="input-group-text">@lang('hours before, refund')</span>
                                            <input type="number" class="form-control" name="rules[0][refund_percentage]" 
                                                   placeholder="%" value="100" required min="0" max="100">
                                            <span class="input-group-text">%</span>
                                            <button type="button" class="btn btn-outline--danger removeRuleBtn">
                                                <i class="la la-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted">@lang('Define refund percentages based on hours before departure. Rules are evaluated from highest to lowest hours.')</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Sort Order')</label>
                                    <input type="number" class="form-control" name="sort_order" 
                                           value="{{ old('sort_order', @$policy->sort_order ?? 0) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check form-switch form--switch pl-0 mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_default" 
                                               {{ old('is_default', @$policy->is_default) ? 'checked' : '' }}>
                                        <label class="form-check-label">@lang('Set as Default Policy')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check form-switch form--switch pl-0 mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               {{ old('is_active', @$policy->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">@lang('Active')</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.cancellation.policy.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-arrow-left"></i>@lang('Back')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let ruleIndex = {{ isset($policy) && $policy->rules ? count($policy->rules) : 1 }};

            $('#addRuleBtn').on('click', function() {
                let ruleHtml = `
                    <div class="rule-row mb-2">
                        <div class="input-group">
                            <span class="input-group-text">@lang('If cancelled')</span>
                            <input type="number" class="form-control" name="rules[${ruleIndex}][hours_before]" 
                                   placeholder="Hours before" value="0" required min="0">
                            <span class="input-group-text">@lang('hours before, refund')</span>
                            <input type="number" class="form-control" name="rules[${ruleIndex}][refund_percentage]" 
                                   placeholder="%" value="0" required min="0" max="100">
                            <span class="input-group-text">%</span>
                            <button type="button" class="btn btn-outline--danger removeRuleBtn">
                                <i class="la la-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#rulesContainer').append(ruleHtml);
                ruleIndex++;
            });

            $(document).on('click', '.removeRuleBtn', function() {
                if ($('.rule-row').length > 1) {
                    $(this).closest('.rule-row').remove();
                } else {
                    notify('error', '@lang('At least one rule is required')');
                }
            });
        })(jQuery);
    </script>
@endpush
