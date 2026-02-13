@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Rule Name')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Value')</th>
                                    <th>@lang('Route')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $rule->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $rule->rule_type_label }}</span>
                                        </td>
                                        <td>{{ $rule->operator_label }}</td>
                                        <td>
                                            @if($rule->operator == 'percentage')
                                                {{ $rule->value }}%
                                            @else
                                                {{ showAmount($rule->value) }}
                                            @endif
                                        </td>
                                        <td>{{ @$rule->route->name ?? 'All Routes' }}</td>
                                        <td>
                                            <span class="badge badge--dark">{{ $rule->priority }}</span>
                                        </td>
                                        <td>
                                            @php echo $rule->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.dynamic-pricing.edit', $rule->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-edit"></i> @lang('Edit')
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline--{{ $rule->is_active ? 'warning' : 'success' }}"
                                                onclick="confirmAction('{{ route('admin.dynamic-pricing.status', $rule->id) }}')">
                                                <i class="las la-{{ $rule->is_active ? 'ban' : 'check-circle' }}"></i>
                                                @lang($rule->is_active ? 'Disable' : 'Enable')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($rules->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($rules) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="{{ route('admin.dynamic-pricing.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add Pricing Rule')
        </a>
        <x-search-form placeholder="Rule Name" />
    </div>
@endpush
