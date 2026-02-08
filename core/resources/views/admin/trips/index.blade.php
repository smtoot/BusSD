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
                                    <th>@lang('Trip Name')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Route')</th>
                                    <th>@lang('Fleet Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trips as $trip)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $trip->title }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$trip->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $trip->owner_id) }}"><span>@</span>{{ @$trip->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$trip->route->name }}</span>
                                        </td>
                                        <td>{{ @$trip->fleetType->name }}</td>
                                        <td>
                                            @php echo $trip->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.trips.show', $trip->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                                 @if($trip->status == 1)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.trips.status', $trip->id) }}"
                                                        data-question="@lang('Are you sure to disable this trip?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.trips.status', $trip->id) }}"
                                                        data-question="@lang('Are you sure to enable this trip?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
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
                @if ($trips->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($trips) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="Trip Name" />
        
        <form action="" method="GET" class="form-inline">
            <div class="input-group">
                <select name="owner_id" class="form-control">
                    <option value="">@lang('All Operators')</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" @selected(request('owner_id') == $owner->id)>{{ $owner->username }}</option>
                    @endforeach
                </select>
                <button class="btn btn--primary input-group-text" type="submit"><i class="la la-search"></i></button>
            </div>
        </form>

        <a href="{{ route('admin.trips.export') }}" class="btn btn-outline--primary"><i class="las la-download"></i> @lang('Export')</a>
    </div>
@endpush
