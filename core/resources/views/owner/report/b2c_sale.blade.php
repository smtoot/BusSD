@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            {{-- Commission Rate Info Banner --}}
            <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                <div>
                    <i class="las la-info-circle"></i>
                    <strong>@lang('Your App Commission Rate'):</strong>
                    <span class="badge badge--primary">{{ $owner->b2c_commission ?? gs('b2c_commission') }}%</span>
                    <small class="ms-2">
                        @if($owner->b2c_commission)
                            @lang('(Custom rate for your company)')
                        @else
                            @lang('(Platform standard rate)')
                        @endif
                    </small>
                </div>
                <a href="{{ route('owner.ticket.open') }}" class="btn btn-sm btn--dark">
                    <i class="las la-question-circle"></i> @lang('Request Rate Review')
                </a>
            </div>

            {{-- Advanced Filters --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('owner.report.sale.b2c') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">@lang('Date Range')</label>
                                <input type="text" name="date" class="form-control date-range" placeholder="@lang('Select Date Range')"
                                    value="{{ request('date') }}" autocomplete="off">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">@lang('Trip / Route')</label>
                                <select name="trip_id" class="form-control">
                                    <option value="">@lang('All Trips')</option>
                                    @foreach($trips as $trip)
                                        <option value="{{ $trip->id }}" {{ request('trip_id') == $trip->id ? 'selected' : '' }}>
                                            {{ $trip->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(isset($branches) && $branches->count() > 1)
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">@lang('Branch')</label>
                                <select name="branch_id" class="form-control">
                                    <option value="">@lang('All Branches')</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }} @if($branch->code)({{ $branch->code }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('All Status')</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Confirmed')</option>
                                    <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>@lang('Cancelled')</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn--primary w-100">
                                    <i class="las la-filter"></i> @lang('Apply Filters')
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <a href="{{ route('owner.report.sale.b2c') }}" class="btn btn--secondary w-100">
                                    <i class="las la-redo"></i> @lang('Reset')
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Active Filters Display --}}
                    @if(request()->hasAny(['date', 'trip_id', 'status']))
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted me-2">@lang('Active Filters'):</small>
                            @if(request('date'))
                                <span class="badge badge--primary me-1">
                                    <i class="las la-calendar"></i> {{ request('date') }}
                                    <a href="{{ route('owner.report.sale.b2c', array_diff_key(request()->all(), ['date' => ''])) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                            @if(request('trip_id'))
                                <span class="badge badge--info me-1">
                                    <i class="las la-route"></i> {{ $trips->where('id', request('trip_id'))->first()->title ?? 'Trip' }}
                                    <a href="{{ route('owner.report.sale.b2c', array_diff_key(request()->all(), ['trip_id' => ''])) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="badge badge--warning me-1">
                                    <i class="las la-check-circle"></i> {{ request('status') == '1' ? __('Confirmed') : __('Cancelled') }}
                                    <a href="{{ route('owner.report.sale.b2c', array_diff_key(request()->all(), ['status' => ''])) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">@lang('Performance Summary')</h5>
                        <button onclick="exportToCSV()" class="btn btn-sm btn--success">
                            <i class="las la-download"></i> @lang('Export to CSV')
                        </button>
                    </div>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5>@lang('Total Gross Volume')</h5>
                            <h3 class="text--primary">{{ gs('cur_sym') }}{{ getAmount($sales->sum(fn($s) => $s->ticket_count * $s->price)) }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h5>@lang('App Passengers')</h5>
                            <h3 class="text--info">{{ $sales->total() }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h5>@lang('Estimated Net Revenue')</h5>
                            <h3 class="text--success">{{ gs('cur_sym') }}{{ getAmount($sales->sum(fn($s) => ($s->ticket_count * $s->price) * (1 - ($owner->b2c_commission ?? gs('b2c_commission')) / 100))) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Journey Date')</th>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Gross Amount')</th>
                                    <th>@lang('Commission')</th>
                                    <th>@lang('Net Credit')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    @php
                                        $gross = $sale->ticket_count * $sale->price;
                                        $commRate = $owner->b2c_commission ?? gs('b2c_commission');
                                        $commission = ($gross * $commRate) / 100;
                                        $net = $gross - $commission;
                                    @endphp
                                    <tr>
                                        <td>{{ showDateTime($sale->date_of_journey, 'M d, Y') }}</td>
                                        <td>
                                            {{ $sale->passenger->firstname }} {{ $sale->passenger->lastname }}
                                            <br>
                                            <small class="text-muted">{{ $sale->passenger->mobile }}</small>
                                        </td>
                                        <td>{{ $sale->trip->title }}</td>
                                        <td class="fw-bold">{{ gs('cur_sym') }}{{ getAmount($gross) }}</td>
                                        <td class="text--danger">{{ gs('cur_sym') }}{{ getAmount($commission) }} ({{ $commRate }}%)</td>
                                        <td class="text--success fw-bold">{{ gs('cur_sym') }}{{ getAmount($net) }}</td>
                                        <td>
                                            @if($sale->status == 1)
                                                <span class="badge badge--success">@lang('Confirmed')</span>
                                            @elseif($sale->status == 3)
                                                <span class="badge badge--warning">@lang('Cancelled')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Other')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No App sales found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($sales->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sales) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

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

    // Define Arabic language for datepicker if not exists
    $.fn.datepicker.language['ar'] = {
        days: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
        daysShort: ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'],
        daysMin: ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'],
        months: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        monthsShort: ['ينا', 'فبر', 'مار', 'أبر', 'ماي', 'يون', 'يول', 'أغس', 'سبت', 'أكت', 'نوف', 'ديس'],
        today: 'اليوم',
        clear: 'مسح',
        dateFormat: 'yyyy-mm-dd',
        timeFormat: 'hh:ii aa',
        firstDay: 6
    };

    // Initialize date range picker
    if ($('.date-range').length) {
        $('.date-range').datepicker({
            range: true,
            multipleDatesSeparator: " - ",
            language: '{{ app()->getLocale() == "ar" ? "ar" : "en" }}',
            dateFormat: 'yyyy-mm-dd',
            autoClose: false
        });
    }
})(jQuery);

function exportToCSV() {
    // Check if there's data to export
    @if($sales->count() == 0)
        notify('error', 'No data available to export');
        return;
    @endif

    // Prepare CSV header with filter information
    let csv = '';

    // Add filter metadata as comments
    @if(request()->hasAny(['date', 'trip_id', 'status']))
        csv += '# App Sales Report - Filtered Results\n';
        @if(request('date'))
            csv += '# Date Range: {{ request("date") }}\n';
        @endif
        @if(request('trip_id'))
            csv += '# Trip: {{ $trips->where("id", request("trip_id"))->first()->title ?? "N/A" }}\n';
        @endif
        @if(request('status'))
            csv += '# Status: {{ request("status") == "1" ? "Confirmed" : "Cancelled" }}\n';
        @endif
        csv += '# Generated: ' + new Date().toLocaleString() + '\n';
        csv += '#\n';
    @endif

    // Add column headers
    csv += 'Journey Date,Passenger Name,Mobile,Trip,Gross Amount,Commission (%),Commission Amount,Net Credit,Status\n';

    // Add data rows (only the filtered results currently displayed)
    @forelse($sales as $sale)
        @php
            $gross = $sale->ticket_count * $sale->price;
            $commRate = $owner->b2c_commission ?? gs('b2c_commission');
            $commission = ($gross * $commRate) / 100;
            $net = $gross - $commission;
        @endphp
        csv += '"{{ showDateTime($sale->date_of_journey, "M d, Y") }}",';
        csv += '"{{ $sale->passenger->firstname }} {{ $sale->passenger->lastname }}",';
        csv += '"{{ $sale->passenger->mobile }}",';
        csv += '"{{ str_replace('"', '""', $sale->trip->title) }}",';
        csv += '"{{ getAmount($gross) }}",';
        csv += '"{{ $commRate }}",';
        csv += '"{{ getAmount($commission) }}",';
        csv += '"{{ getAmount($net) }}",';
        @if($sale->status == 1)
            csv += '"Confirmed"\n';
        @elseif($sale->status == 3)
            csv += '"Cancelled"\n';
        @else
            csv += '"Other"\n';
        @endif
    @empty
    @endforelse

    // Generate filename with filter info
    let filename = 'app_sales';
    @if(request('date'))
        filename += '_{{ str_replace(" - ", "_to_", request("date")) }}';
    @else
        filename += '_' + new Date().toISOString().split('T')[0];
    @endif
    @if(request('trip_id'))
        filename += '_trip{{ request("trip_id") }}';
    @endif
    @if(request('status'))
        filename += '_{{ request("status") == "1" ? "confirmed" : "cancelled" }}';
    @endif
    filename += '.csv';

    // Create and trigger download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    notify('success', 'CSV exported: {{ $sales->total() }} record(s)');
}
</script>
@endpush
