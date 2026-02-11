@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('PNR')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Passenger/Counter')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Boarding')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $booking->trx }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$booking->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $booking->owner_id) }}"><span>@</span>{{ @$booking->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            {{ @$booking->trip->title }}
                                            <br>
                                            <small class="text-muted">{{ @$booking->trip->route->name }}</small>
                                        </td>
                                        <td>
                                            @if($booking->passenger_id)
                                                <span class="fw-bold">{{ @$booking->passenger->fullname }}</span>
                                                <br>
                                                <small class="text-muted">@lang('B2C')</small>
                                            @else
                                                <span class="fw-bold">{{ @$booking->counterManager->fullname }}</span>
                                                <br>
                                                <small class="text-muted">@lang('Counter'): {{ @$booking->counterManager->counter->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($booking->price) }}</span>
                                        </td>
                                        <td>
                                            @if($booking->is_boarded)
                                                <span class="badge badge--success">@lang('Boarded')</span>
                                                <br>
                                                <small class="text-muted">{{ showDateTime($booking->boarded_at, 'h:i A') }}</small>
                                            @else
                                                <span class="badge badge--dark">@lang('Not Boarded')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->status == 1)
                                                <span class="badge badge--success">@lang('Confirmed')</span>
                                            @elseif($booking->status == 3)
                                                <span class="badge badge--danger">@lang('Cancelled')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($bookings->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($bookings) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="{{ __('PNR / Email / Username') }}" />
        <a href="{{ route('admin.bookings.export') }}" class="btn btn-outline--primary"><i class="las la-download"></i> @lang('Export')</a>
    </div>
@endpush
