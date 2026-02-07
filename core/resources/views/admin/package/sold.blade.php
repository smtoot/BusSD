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
                                <th>@lang('S.N.')</th>
                                <th>@lang('Order Number')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Time Limit')</th>
                                <th>@lang('Status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($packages as $package)
                            <tr>
                                <td data-label="@lang('S.N.')">{{$package->current_page-1 * $package ->per_page + $loop->iteration }}</td>
                                <td data-label="@lang('Order Number')">{{ $package->order_number }}</td>
                                <td data-label="@lang('Name')">{{ $package->package->name }}</td>
                                <td data-label="@lang('Price')">{{$general->cur_sym}}{{ $package->price }}</td>
                                <td data-label="@lang('Time Limit')"> {{ showDateTime($package->ends_at, 'd M, Y') }} {{getPackageLimitUnit($package->unit)}}</td>

                                <td data-label="@lang('Status')">
                                    <span class="text--small badge font-weight-normal badge--{{$package->status?'success':'danger'}}">
                                        {{$package->status? trans('Active'):trans('Inactive')}}
                                    </span>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ $packages->links('admin.partials.paginate') }}
                </div>
            </div><!-- card end -->
        </div>
    </div>
@endsection


