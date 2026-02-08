@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Layout Name')</th>
                                    <th>@lang('Configuration')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seatLayouts as $seatLayout)
                                    <tr>
                                        <td>{{ $seatLayout->name ?: $seatLayout->layout }}</td>
                                        <td>
                                            @if($seatLayout->schema)
                                                <span class="badge badge--success">@lang('Visual Mapping Enabled')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Standard Layout')</span>
                                            @endif
                                        </td>
                                        <td>@php echo $seatLayout->statusBadge; @endphp</td>
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
                @if ($seatLayouts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($seatLayouts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <span class="badge badge--info p-2">
        <i class="las la-info-circle"></i> @lang('Templates are managed by Admin')
    </span>
@endpush
