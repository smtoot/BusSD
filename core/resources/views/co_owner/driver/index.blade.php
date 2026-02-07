@extends('co_owner.layouts.app')
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
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $driver)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('driver') . '/' . @$driver->image, getFileSize('driver')) }}"
                                                        alt="image">
                                                    <span class="name">{{ $driver->fullname }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span>@</span>{{ $driver->username }}</td>
                                        <td>{{ $driver->email }}</td>
                                        <td>@php echo $driver->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('co-owner.driver.form', $driver->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>
                                                @if ($driver->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to active this driver?')"
                                                        data-action="{{ route('co-owner.driver.status', $driver->id) }}">
                                                        <i class="la la-eye"></i>@lang('Active')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to inactive this driver?')"
                                                        data-action="{{ route('co-owner.driver.status', $driver->id) }}">
                                                        <i class="la la-eye-slash"></i>@lang('Inactive')
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
                @if ($users->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($users) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <a href="{{ route('co-owner.driver.form') }}" class="btn btn-outline--primary">
        <i class="fas fa-plus"></i> @lang('Add New')
    </a>
@endpush
