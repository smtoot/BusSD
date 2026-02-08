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
                                    <th>@lang('Route Name')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Starting Point')</th>
                                    <th>@lang('Destination')</th>
                                    <th>@lang('Trips')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $route->name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$route->owner->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $route->owner_id) }}"><span>@</span>{{ @$route->owner->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ @$route->startingPoint->name }}</td>
                                        <td>{{ @$route->destinationPoint->name }}</td>
                                        <td>{{ $route->trips_count }}</td>
                                        <td>
                                            @php echo $route->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.routes.show', $route->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
                                            <a href="{{ route('admin.routes.edit', $route->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-edit"></i> @lang('Edit')
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline--danger"
                                                onclick="confirmDelete('{{ route('admin.routes.delete', $route->id) }}')">
                                                <i class="las la-trash"></i> @lang('Delete')
                                            </button>
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
                @if ($routes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($routes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="{{ route('admin.routes.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add New Route')
        </a>
        <x-search-form placeholder="Route Name" />
    </div>
@endpush
