@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Origin')</th>
                                    <th>@lang('Destination')</th>
                                    <th>@lang('Distance')</th>
                                    <th>@lang('Time')</th>
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
                                        <td>{{ @$route->startingPoint->name }}</td>
                                        <td>{{ @$route->destinationPoint->name }}</td>
                                        <td>{{ $route->distance }} @lang('km')</td>
                                        <td>{{ $route->time }} @lang('hrs')</td>
                                        <td>{{ $route->trips_count }}</td>
                                        <td>
                                            @php echo $route->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('owner.route.edit', $route->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-edit"></i> @lang('Edit')
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline--info statusBtn"
                                                data-id="{{ $route->id }}"
                                                data-status="{{ $route->status }}">
                                                @if($route->status == 1)
                                                    <i class="las la-eye-slash"></i> @lang('Disable')
                                                @else
                                                    <i class="las la-eye"></i> @lang('Enable')
                                                @endif
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __('No routes found') }}</td>
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

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="{{ route('owner.route.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add New Variation')
        </a>
        <x-search-form placeholder="Route Name" />
    </div>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            $('.statusBtn').on('click', function () {
                var modal = $('#confirmationModal');
                var text = $(this).data('status') == 1 ? "@lang('Are you sure to disable this route?')" : "@lang('Are you sure to enable this route?')";
                modal.find('.question').text(text);
                modal.find('form').attr('action', `{{ route('owner.route.status', '') }}/${$(this).data('id')}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
