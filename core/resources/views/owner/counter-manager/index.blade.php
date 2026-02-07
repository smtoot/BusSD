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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Counter')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $manager)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('counter_manager') . '/' . @$manager->image, getFileSize('counter_manager')) }}"
                                                        alt="image">
                                                    <span class="name">{{ $manager->fullname }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span>@</span>{{ $manager->username }}</td>
                                        <td>{{ $manager->email }}</td>
                                        <td>{{ $manager->counter ? $manager->counter->name : 'N/A' }}</td>
                                        <td>@php echo $manager->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('owner.counter.manager.form', $manager->id) }}"
                                                    class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>
                                                @if ($manager->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to active this counter manager?')"
                                                        data-action="{{ route('owner.counter.manager.status', $manager->id) }}">
                                                        <i class="la la-eye"></i>@lang('Active')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to inactive this counter manager?')"
                                                        data-action="{{ route('owner.counter.manager.status', $manager->id) }}">
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
    <a href="{{ route('owner.counter.manager.form') }}" class="btn btn-outline--primary">
        <i class="fas fa-plus"></i> @lang('Add New')
    </a>
@endpush
