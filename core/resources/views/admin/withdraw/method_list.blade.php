@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Method')</th>
                                    <th>@lang('Limits')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Processing Time')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($methods as $method)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($method->image)
                                                    <img src="{{ getImage(getFilePath('withdrawMethod') . '/' . $method->image, getFileSize('withdrawMethod')) }}" alt="{{ $method->name }}" class="rounded" width="40">
                                                @endif
                                                <span class="fw-bold">{{ __($method->name) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{ showAmount($method->min_limit) }} - {{ showAmount($method->max_limit) }}
                                        </td>
                                        <td>
                                            {{ showAmount($method->fixed_charge) }} + {{ getAmount($method->percent_charge) }}%
                                        </td>
                                        <td>
                                            {{ __($method->delay) }}
                                        </td>
                                        <td>
                                            @if($method->status == 1)
                                                <span class="badge badge--success">@lang('Enabled')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Disabled')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.withdraw.method.edit', $method->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                @if($method->status == 1)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.withdraw.method.status', $method->id) }}"
                                                        data-question="@lang('Are you sure to disable this method?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.withdraw.method.status', $method->id) }}"
                                                        data-question="@lang('Are you sure to enable this method?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
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
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.withdraw.method.create') }}" class="btn btn-sm btn--primary">
        <i class="las la-plus"></i> @lang('Add New')
    </a>
@endpush
