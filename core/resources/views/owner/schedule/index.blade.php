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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Route')</th>
                                    <th>@lang('Fleet Type')</th>
                                    <th>@lang('Schedule')</th>
                                    <th>@lang('Recurrence')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules ?? [] as $schedule)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text--primary">{{ __($schedule->name) }}</span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-layer-group me-1"></i> {{ __($schedule->trip_type) }} |
                                                <i class="fas fa-medal me-1 ml-1"></i> {{ __($schedule->trip_category) }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ optional($schedule->route)->name }}</span>
                                            <br>
                                            <small class="text-muted">
                                                <span class="text--success">{{ optional($schedule->startingPoint)->name }}</span>
                                                <i class="fas fa-long-arrow-alt-right mx-1"></i>
                                                <span class="text--danger">{{ optional($schedule->destinationPoint)->name }}</span>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge--light text--dark border">
                                                <i class="fas fa-bus me-1"></i> {{ optional($schedule->fleetType)->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text--dark fw-bold">
                                                {{ showDateTime($schedule->starts_from, 'h:i a') }} - {{ showDateTime($schedule->ends_at, 'h:i a') }}
                                            </div>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i> {{ $schedule->duration_hours }}h {{ $schedule->duration_minutes }}m
                                            </small>
                                        </td>
                                        <td>
                                            @if($schedule->recurrence_type == 'daily')
                                                <span class="badge badge--success shadow-sm">
                                                    <i class="fas fa-redo me-1"></i> @lang('Daily')
                                                </span>
                                            @else
                                                <span class="badge badge--info shadow-sm">
                                                    <i class="fas fa-calendar-alt me-1"></i> @lang('Weekly')
                                                </span>
                                                <br>
                                                <small class="text-muted fw-bold">
                                                    @php
                                                        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                                        $selectedDays = array_map(function($d) use ($days) { return __($days[$d]); }, $schedule->recurrence_days ?? []);
                                                        echo implode(', ', $selectedDays);
                                                    @endphp
                                                </small>
                                            @endif
                                        </td>
                                        <td>@php echo $schedule->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.trip.schedule.edit', $schedule->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                @if ($schedule->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this schedule template?')"
                                                        data-action="{{ route('owner.trip.schedule.status', $schedule->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this schedule template?')"
                                                        data-action="{{ route('owner.trip.schedule.status', $schedule->id) }}">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
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
                @if (@$schedules->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$schedules) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('owner.trip.schedule.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="fas fa-plus"></i> @lang('Add New Template')
    </a>
@endpush
