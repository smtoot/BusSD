@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Icon')</th>
                                    <th>@lang('Amenity')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($amenities as $amenity)
                                    <tr>
                                        <td>
                                            <i class="fa {{ $amenity->icon }} fa-2x text-primary"></i>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __($amenity->label) }}</span>
                                            @if($amenity->is_system)
                                                <span class="badge badge--primary">@lang('System')</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $amenity->key }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge--dark">{{ __($amenity->category_label) }}</span>
                                        </td>
                                        <td>@php echo $amenity->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.amenity.template.edit', $amenity->id) }}" 
                                                   class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>
                                                @if ($amenity->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this amenity?')"
                                                        data-action="{{ route('admin.amenity.template.status', $amenity->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this amenity?')"
                                                        data-action="{{ route('admin.amenity.template.status', $amenity->id) }}">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @endif
                                                @if(!$amenity->is_system)
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this amenity?')"
                                                        data-action="{{ route('admin.amenity.template.delete', $amenity->id) }}">
                                                        <i class="la la-trash"></i>@lang('Delete')
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
                @if ($amenities->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($amenities) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <a href="{{ route('admin.amenity.template.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-plus"></i>@lang('Add New Amenity')
    </a>
@endpush
