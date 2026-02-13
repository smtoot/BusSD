@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('Landmark')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($droppingPoints as $point)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $point->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--info">{{ $point->type_label }}</span>
                                        </td>
                                        <td>{{ @$point->city->name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($point->landmark ?? 'N/A', 30) }}</td>
                                        <td>
                                            @if($point->owner_id == 0)
                                                <span class="badge badge--success">@lang('Global')</span>
                                            @else
                                                <span class="fw-bold">{{ @$point->owner->fullname }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php echo $point->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.dropping-points.edit', $point->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-edit"></i> @lang('Edit')
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline--{{ $point->is_active ? 'warning' : 'success' }}"
                                                onclick="confirmAction('{{ route('admin.dropping-points.status', $point->id) }}')">
                                                <i class="las la-{{ $point->is_active ? 'ban' : 'check-circle' }}"></i>
                                                @lang($point->is_active ? 'Disable' : 'Enable')
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
                @if ($droppingPoints->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($droppingPoints) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="{{ route('admin.dropping-points.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add Dropping Point')
        </a>
        <x-search-form placeholder="Name / Landmark" />
    </div>
@endpush
