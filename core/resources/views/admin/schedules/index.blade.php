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
                                    <th>@lang('Schedule')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Active Trips')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $schedule->start_from }} - {{ $schedule->end_at }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$schedule->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $schedule->owner_id) }}"><span>@</span>{{ @$schedule->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $schedule->trips_count }}</td>
                                        <td>
                                            @php echo $schedule->statusBadge @endphp
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
                @if ($schedules->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($schedules) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="Start Time / End Time" />
    </div>
@endpush
