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
                                    <th>@lang('Purchased Date')</th>
                                    <th>@lang('Expired Date')</th>
                                    <th>@lang('Order No.')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Owner')</th>
                                    <th>@lang('Package')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Price')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ showDateTime($sale->starts_from) }}</td>
                                        <td>{{ showDateTime($sale->ends_at) }}</td>
                                        <td>{{ $sale->order_number }}</td>
                                        <td>{{ @$sale->deposit->trx }}</td>
                                        <td>
                                            {{ $sale->owner->fullname }}
                                            <br>
                                            <a href="{{ route('admin.users.detail', $sale->owner_id) }}">
                                                <span>@</span>{{ @$sale->owner->username }}
                                            </a>
                                        </td>
                                        <td class="fw-bold">{{ __($sale->package->name) }}</td>
                                        <td>@php echo $sale->statusBadge; @endphp </td>
                                        <td><strong>{{ showAmount($sale->price) }}</strong></td>
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
                @if ($sales->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sales) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Order No. / Username" />
@endpush
