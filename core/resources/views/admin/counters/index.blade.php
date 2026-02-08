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
                                    <th>@lang('Counter Name')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($counters as $counter)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $counter->name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$counter->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $counter->owner_id) }}"><span>@</span>{{ @$counter->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $counter->city }}</td>
                                        <td>{{ $counter->location }}</td>
                                        <td>
                                            @php echo $counter->statusBadge @endphp
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
                @if ($counters->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($counters) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="Name / City / Location" />
    </div>
@endpush
