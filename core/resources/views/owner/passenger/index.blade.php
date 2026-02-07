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
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Mobile | Email')</th>
                                    <th>@lang('Total Bookings')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($passengers as $passenger)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $passenger->firstname }} {{ $passenger->lastname }}</span>
                                        </td>
                                        <td>
                                            {{ $passenger->mobile }} <br>
                                            {{ $passenger->email }}
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $passenger->booked_tickets_count }}</span>
                                        </td>
                                        <td>
                                            {{ showDateTime($passenger->created_at) }} <br> 
                                            {{ diffForHumans($passenger->created_at) }}
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.passenger.history', $passenger->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-history"></i> @lang('History')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No passengers found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($passengers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($passengers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search Passenger..." />
@endpush
