@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Trip | Route')</th>
                                    <th>@lang('Ticket ID')</th>
                                    <th>@lang('Seats')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>
                                            {{ showDateTime($booking->date_of_journey, 'M d, Y') }}<br>
                                            <small>{{ showDateTime($booking->created_at, 'M d, Y h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $booking->trip->title }}</span> <br>
                                            <small>{{ $booking->trip->route->name }}</small>
                                        </td>
                                        <td>{{ $booking->trx }}</td>
                                        <td>
                                            {{ implode(', ', $booking->seats) }} <br>
                                            <small>({{ $booking->ticket_count }} @lang('Seats'))</small>
                                        </td>
                                        <td>
                                            {{ gs('cur_sym') }}{{ getAmount($booking->price) }}
                                        </td>
                                        <td>
                                            @php echo $booking->statusBadge; @endphp
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No bookings found') }}</td>
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
    <a href="{{ route('owner.passenger.index') }}" class="btn btn-sm btn-outline--dark">
        <i class="la la-reply"></i> @lang('Back to List')
    </a>
@endpush
