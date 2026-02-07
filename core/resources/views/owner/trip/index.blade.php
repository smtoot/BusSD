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
                                    <th>@lang('Title')</th>
                                    <th>@lang('AC / Non AC')</th>
                                    <th>@lang('Day Off')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trips ?? [] as $trip)
                                    <tr>
                                        <td>{{ $trip->title }}</td>
                                        <td>{{ $trip->fleetType->has_ac ? trans('Ac') : trans('Non Ac') }}</td>
                                        <td>
                                            @if ($trip->day_off)
                                                @foreach ($trip->day_off as $item)
                                                    {{ showDayOff($item) }}
                                                @endforeach
                                            @else
                                                @lang('No Off Day')
                                            @endif
                                        </td>
                                        <td>@php echo $trip->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.trip.form', $trip->id) }}"
                                                    class="btn btn-sm btn-outline--primary editBtn">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                @if ($trip->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this trip?')"
                                                        data-action="{{ route('owner.trip.status', $trip->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this trip?')"
                                                        data-action="{{ route('owner.trip.status', $trip->id) }}">
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
                @if (@$trips->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks(@$trips) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <a href="{{ route('owner.trip.form') }}" class="btn btn-sm btn-outline--primary">
        <i class="fas fa-plus"></i> @lang('Add New')
    </a>
@endpush
