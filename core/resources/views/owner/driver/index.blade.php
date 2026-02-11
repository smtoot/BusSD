@extends('owner.layouts.app')
@section('panel')
    {{-- KPI Cards Section --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-users overlay-icon text--primary"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="las la-users"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{ $stats['total'] }}</h2>
                    <p>@lang('Total Drivers')</p>
                </div>
            </div><!-- widget-two end -->
        </div>
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-user-check overlay-icon text--success"></i>
                <div class="widget-two__icon b-radius--5 bg--success">
                    <i class="las la-user-check"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{ $stats['active'] }}</h2>
                    <p>@lang('Active Drivers')</p>
                </div>
            </div><!-- widget-two end -->
        </div>
        <div class="col-xl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                <i class="las la-exclamation-triangle overlay-icon text--warning"></i>
                <div class="widget-two__icon b-radius--5 bg--warning">
                    <i class="las la-exclamation-triangle"></i>
                </div>
                <div class="widget-two__content">
                    <h2 class="">{{ $stats['expiring_soon'] }}</h2>
                    <p>@lang('Expiring Soon')</p>
                </div>
            </div><!-- widget-two end -->
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Driver Name')</th>
                                    <th>@lang('Phone Number')</th>
                                    <th>@lang('License Number')</th>
                                    <th>@lang('License Expiry Date')</th>
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
                                        <td>
                                            <i class="las la-phone"></i> {{ $driver->dial_code }}{{ $driver->mobile }}
                                        </td>
                                        <td>
                                            <i class="las la-id-card"></i> {{ $driver->license_number ?? __('N/A') }}
                                        </td>
                                        <td>
                                            @if($driver->license_expiry_date)
                                                <i class="las la-calendar"></i> {{ showDateTime($driver->license_expiry_date, 'm/d/Y') }}
                                                @if($driver->license_expiry_date <= now())
                                                    <span class="badge badge--danger ms-1">@lang('Expired')</span>
                                                @elseif($driver->license_expiry_date <= now()->addDays(30))
                                                    <span class="badge badge--warning ms-1">@lang('Near Expiry')</span>
                                                @endif
                                            @else
                                                @lang('N/A')
                                            @endif
                                        </td>
                                        <td>
                                            @if($driver->status == Status::ENABLE)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.driver.form', $driver->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>
                                                @if ($driver->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to active this driver?')"
                                                        data-action="{{ route('owner.driver.status', $driver->id) }}">
                                                        <i class="la la-eye"></i>@lang('Active')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to inactive this driver?')"
                                                        data-action="{{ route('owner.driver.status', $driver->id) }}">
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
    <a href="{{ route('owner.driver.form') }}" class="btn btn-outline--primary">
        <i class="fas fa-plus"></i> @lang('Add New Driver')
    </a>
@endpush
