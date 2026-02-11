@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="header-nav mb-4">
                <a href="{{ route('owner.bookings.index') }}" class="text-muted"><i class="las la-arrow-right"></i> @lang('Back to Bookings')</a>
                <h2 class="fw-bold mt-2">{{ __($trip->title) }}</h2>
                <div class="d-flex align-items-center text-muted">
                    <span class="me-3"><i class="las la-map-marker"></i> {{ __($trip->startingPoint->name) }} &rarr; {{ __($trip->destinationPoint->name) }}</span>
                    <span><i class="las la-clock"></i> {{ Carbon\Carbon::parse($trip->departure_datetime)->format('H:i') }}</span>
                </div>
            </div>

            <div class="header-actions d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('owner.trip.form', $trip->id) }}" class="btn btn--success"><i class="las la-plus"></i> @lang('Add New Booking')</a>
                <button class="btn btn-outline-primary"><i class="las la-paper-plane"></i> @lang('Notify Passengers')</button>
                <button class="btn btn-outline-secondary"><i class="las la-print"></i> @lang('Print Manifest')</button>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 b-radius--10 border-start border-success border-4 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box bg--success-light p-3 b-radius--10 me-3">
                                <i class="las la-check-circle la-2x text-success"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">@lang('Checked-in')</p>
                                <h3 class="fw-bold mb-0" id="boarded-count">{{ $trip->boardedCount() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 b-radius--10 border-start border-primary border-4 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box bg--primary-light p-3 b-radius--10 me-3">
                                <i class="las la-users la-2x text-primary"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">@lang('Total Passengers')</p>
                                <h3 class="fw-bold mb-0" id="booked-count">{{ $trip->bookedCount() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 b-radius--10 border-start border-warning border-4 h-100">
                        <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="text-muted mb-0 small">@lang('Check-in Progress')</p>
                                        @php $occ = $trip->fleetCapacity() > 0 ? round(($trip->bookedCount() / $trip->fleetCapacity()) * 100) : 0; @endphp
                                        <h3 class="fw-bold mb-0" id="progress-text">{{ $occ }}%</h3>
                                    </div>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-success" id="progress-bar" role="progressbar" style="width: {{ $trip->checkinProgress() }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card b-radius--10 shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                                <input type="text" id="passenger-search" class="form-control border-start-0 ps-0" placeholder="@lang('Search by passenger name, booking ID, seat number')...">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <select class="form-select border-0 bg-light" id="filter-boarding">
                                <option value="all">@lang('All Passengers')</option>
                                <option value="boarded">@lang('Boarded')</option>
                                <option value="not-boarded">@lang('Not Boarded')</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select class="form-select border-0 bg-light" id="filter-source">
                                <option value="all">@lang('All Sources')</option>
                                <option value="App">@lang('BusConnect (App)')</option>
                                <option value="Counter">@lang('Manual Booking')</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#seatMapCollapse">
                                <i class="las la-th"></i> <span id="seatMapToggleText">@lang('Show Seat Map')</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse mb-4" id="seatMapCollapse">
                <div class="card b-radius--10 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-8" id="seat-map-container">
                                @include('owner.bookings.partials.seat_map', ['trip' => $trip, 'passengers' => $passengers])
                            </div>
                            <div class="col-lg-4 border-start px-4">
                                <h5 class="fw-bold mb-4">@lang('Seat Map Details')</h5>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">Total Capacity</span>
                                        <span class="fw-bold">{{ $trip->fleetCapacity() }} seats</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span class="text-muted">@lang('Occupancy Rate')</span>
                                        @php $occ = $trip->fleetCapacity() > 0 ? round(($trip->bookedCount() / $trip->fleetCapacity()) * 100) : 0; @endphp
                                        <span class="fw-bold" dir="ltr">{{ $occ }}%</span>
                                    </div>
                                </div>
                                <div class="legend">
                                    <p class="small text-muted mb-2">@lang('Legend')</p>
                                    <div class="d-flex align-items-center mb-2 small">
                                        <div class="seat-sample bg-light border me-2"></div> @lang('Available')
                                    </div>
                                    <div class="d-flex align-items-center mb-2 small">
                                        <div class="seat-sample bg--primary me-2"></div> @lang('BusConnect Sales')
                                    </div>
                                    <div class="d-flex align-items-center mb-2 small">
                                        <div class="seat-sample bg-dark text-white d-flex align-items-center justify-content-center me-2" style="font-size: 8px;">M</div> @lang('Manual Booking')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card b-radius--10 shadow-sm border-0 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive--md">
                        <table class="table table--light style--two custom-data-table mb-0" id="passengers-table">
                            <thead>
                                <tr>
                                    <th>@lang('Seat')</th>
                                    <th>@lang('Passenger Name')</th>
                                    <th>@lang('Booking ID')</th>
                                    <th>@lang('Source')</th>
                                    <th>@lang('Check-in Status')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($passengers as $p)
                                    <tr class="passenger-row" data-name="{{ strtolower($p->name) }}" data-booking="{{ strtolower($p->booking_id) }}" data-seat="{{ strtolower($p->seat_no) }}" data-source="{{ $p->source }}" data-boarded="{{ $p->is_boarded ? 'boarded' : 'not-boarded' }}">
                                        <td class="fw-bold">{{ $p->seat_no }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $p->name }}</div>
                                            <div class="small text-muted">{{ $p->phone }}</div>
                                        </td>
                                        <td><span class="badge badge--light text--dark border">{{ $p->booking_id }}</span></td>
                                        <td>
                                            @if($p->source == 'App')
                                                <span class="badge badge--success-light text-success">BusConnect</span>
                                            @else
                                                <span class="badge badge--primary-light text-primary">Manual</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch d-flex align-items-center">
                                                <input class="form-check-input checkin-toggle" type="checkbox" data-id="{{ $p->ticket_id }}" {{ $p->is_boarded ? 'checked' : '' }}>
                                                <span class="ms-2 status-label {{ $p->is_boarded ? 'text-success' : 'text-muted' }}">
                                                    {{ $p->is_boarded ? __('Boarded') : __('Not Boarded') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="la la-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="las la-edit"></i> @lang('Edit Booking')</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="las la-sms"></i> @lang('Resend Ticket')</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="las la-trash"></i> @lang('Cancel Booking')</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center p-5">
                                            <div class="text-muted">@lang('No passengers found for this trip.')</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .bg--success-light { background-color: #E6F6ED; }
    .bg--primary-light { background-color: #EBF3FF; }
    .badge--success-light { background-color: #E6F6ED; color: #00A34D; }
    .badge--primary-light { background-color: #EBF3FF; color: #1F78D1; }
    .btn--success { background-color: #00A34D !important; color: white !important; }
    .seat-sample { width: 20px; height: 20px; border-radius: 4px; }
    .form-check-input:checked { background-color: #00A34D; border-color: #00A34D; }
    .passenger-row { transition: background-color 0.3s; }
    .passenger-row.newly-boarded { background-color: #f0fff4; }
</style>
@endpush

@push('script')
<script>
    $(function() {
        // Search Filter
        $('#passenger-search').on('keyup', function() {
            filterRows();
        });

        $('#filter-boarding, #filter-source').on('change', function() {
            filterRows();
        });

        function filterRows() {
            let search = $('#passenger-search').val().toLowerCase();
            let boarding = $('#filter-boarding').val();
            let source = $('#filter-source').val();

            $('.passenger-row').each(function() {
                let row = $(this);
                let textMatch = row.data('name').includes(search) || row.data('booking').includes(search) || row.data('seat').includes(search);
                let boardingMatch = boarding === 'all' || row.data('boarded') === boarding;
                let sourceMatch = source === 'all' || row.data('source') === source;

                if (textMatch && boardingMatch && sourceMatch) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }

        // AJAX Check-in
        $('.checkin-toggle').on('change', function() {
            let checkbox = $(this);
            let id = checkbox.data('id');
            let status = checkbox.is(':checked') ? 1 : 0;
            let label = checkbox.siblings('.status-label');
            let row = checkbox.closest('tr');

            checkbox.prop('disabled', true);

            $.ajax({
                url: `{{ route('owner.bookings.checkin', '') }}/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        notify('success', response.message);
                        
                        // Update UI for ALL rows with this booking ID (if multiple seats)
                        // Note: The controller check-in routes based on ticket ID, which covers all seats
                        // So finding other rows with same data-id (ticket_id) or same booking-id logic validation
                        
                        // In our flattened loop, data-id is ticket_id. So all rows for this ticket share data-id.
                        let relatedCheckboxes = $(`.checkin-toggle[data-id="${id}"]`);
                        
                        relatedCheckboxes.each(function() {
                            let cb = $(this);
                            let currentRow = cb.closest('tr');
                            let currentLabel = cb.siblings('.status-label');
                            
                            // Sync checkbox state if not the one clicked
                            if (cb[0] !== checkbox[0]) {
                                cb.prop('checked', status === 1);
                            }
                            
                            if (status) {
                                currentLabel.text("@lang('Checked-in')").removeClass('text-muted').addClass('text-success');
                                currentRow.data('boarded', 'boarded');
                            } else {
                                currentLabel.text("@lang('Not checked-in')").removeClass('text-success').addClass('text-muted');
                                currentRow.data('boarded', 'not-boarded');
                            }
                        });

                        // Update stats
                        $('#boarded-count').text(response.boarded_count);
                        $('#progress-text').text(response.progress + '%');
                        $('#progress-bar').css('width', response.progress + '%');
                    }
                },
                error: function() {
                    notify('error', 'Something went wrong');
                    checkbox.prop('checked', !status);
                },
                complete: function() {
                    checkbox.prop('disabled', false);
                    // Re-enable related checkboxes too if we disabled them validation
                    $(`.checkin-toggle[data-id="${id}"]`).prop('disabled', false);
                }
            });
        });

        // Toggle Seat Map Text
        $('#seatMapCollapse').on('show.bs.collapse', function () {
            $('#seatMapToggleText').text("@lang('Hide Seat Map')");
        }).on('hide.bs.collapse', function () {
            $('#seatMapToggleText').text("@lang('Show Seat Map')");
        });
    });
</script>
@endpush
