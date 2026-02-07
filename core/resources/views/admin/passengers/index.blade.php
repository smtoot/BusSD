@extends('admin.layouts.app')
@section('panel')
    {{-- Statistics Cards --}}
    <div class="row gy-4 mb-4">
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--primary has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-users f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Passengers')</span>
                            <h2 class="text-white">{{ $totalPassengers }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--success has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-user-check f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Active Passengers')</span>
                            <h2 class="text-white">{{ $activePassengers }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--danger has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-user-slash f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Banned Passengers')</span>
                            <h2 class="text-white">{{ $bannedPassengers }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--info has-link box--shadow2 overflow-hidden">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-user-plus f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('New This Month')</span>
                            <h2 class="text-white">{{ $newThisMonth }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter --}}
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.passengers.index') }}" method="GET">
                        <div class="d-flex flex-wrap gap-3">
                            <div class="flex-grow-1">
                                <label class="form-label">@lang('Search')</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="@lang('Name, Email, or Mobile')">
                            </div>
                            <div>
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('All')</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Active')</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('Banned')</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">@lang('Date Range')</label>
                                <input type="text" name="date" class="form-control date-range" value="{{ request('date') }}" placeholder="@lang('Select Date')" autocomplete="off">
                            </div>
                            <div class="align-self-end">
                                <button type="submit" class="btn btn--primary h-45"><i class="las la-filter"></i> @lang('Filter')</button>
                            </div>
                            <div class="align-self-end">
                                <a href="{{ route('admin.passengers.index') }}" class="btn btn--dark h-45"><i class="las la-redo-alt"></i> @lang('Reset')</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Passengers Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Email - Mobile')</th>
                                    <th>@lang('Total Bookings')</th>
                                    <th>@lang('Total Spent')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($passengers as $passenger)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $passenger->firstname }} {{ $passenger->lastname }}</span>
                                            <br>
                                            <small class="text-muted">ID: {{ $passenger->id }}</small>
                                        </td>
                                        <td>
                                            {{ $passenger->email }}
                                            <br>
                                            {{ $passenger->mobile }}
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $passenger->total_bookings ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($passenger->total_spent ?? 0) }}</span>
                                        </td>
                                        <td>
                                            @if($passenger->status == 1)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Banned')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($passenger->created_at, 'M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ diffForHumans($passenger->created_at) }}</small>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.passengers.show', $passenger->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-eye"></i> @lang('Details')
                                                </a>
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
                @if($passengers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($passengers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.passengers.export', request()->all()) }}" class="btn btn-sm btn--success">
        <i class="las la-download"></i> @lang('Export CSV')
    </a>
@endpush

@push('script-lib')
<script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('style-lib')
<link href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}" rel="stylesheet">
@endpush

@push('script')
<script>
(function ($) {
    "use strict";

    // Initialize date range picker
    if ($('.date-range').length) {
        $('.date-range').datepicker({
            range: true,
            multipleDatesSeparator: " - ",
            language: 'en',
            dateFormat: 'yyyy-mm-dd',
            autoClose: false
        });
    }
})(jQuery);
</script>
@endpush
