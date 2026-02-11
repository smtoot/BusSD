@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Policy Name')</th>
                                    <th>@lang('Refund Rules')</th>
                                    <th>@lang('Default')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($policies as $policy)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($policy->label) }}</span>
                                            @if($policy->is_system)
                                                <span class="badge badge--primary">@lang('System')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                @foreach($policy->formatted_rules as $rule)
                                                    <div>{{ $rule['refund'] }} refund up to {{ $rule['time'] }}</div>
                                                @endforeach
                                            </small>
                                        </td>
                                        <td>
                                            @if($policy->is_default)
                                                <span class="badge badge--success">@lang('Yes')</span>
                                            @else
                                                <span class="text-muted">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>@php echo $policy->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.cancellation.policy.edit', $policy->id) }}" 
                                                   class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>
                                                @if ($policy->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this policy?')"
                                                        data-action="{{ route('admin.cancellation.policy.status', $policy->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this policy?')"
                                                        data-action="{{ route('admin.cancellation.policy.status', $policy->id) }}">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @endif
                                                @if(!$policy->is_system)
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this policy?')"
                                                        data-action="{{ route('admin.cancellation.policy.delete', $policy->id) }}">
                                                        <i class="la la-trash"></i>@lang('Delete')
                                                    </button>
                                                @endif
                                            </div>
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
                @if ($policies->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($policies) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <a href="{{ route('admin.cancellation.policy.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-plus"></i>@lang('Add New Policy')
    </a>
@endpush
