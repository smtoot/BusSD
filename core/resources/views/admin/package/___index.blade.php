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
                                <th>@lang('Name')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Time Limit')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($packages as $package)
                            <tr>
                                <td data-label="@lang('S.N.')">{{$package ->current_page-1 * $package ->per_page + $loop->iteration }}</td>
                                <td data-label="@lang('Name')">{{ $package->name }}</td>
                                <td data-label="@lang('Price')">{{$general->cur_sym}}{{ $package->price }}</td>

                                <td data-label="@lang('Time Limit')">{{ $package->time_limit }} {{ getPackageLimitUnit($package->unit) }}</td>

                                <td data-label="@lang('Status')">
                                <span class="text--small badge font-weight-normal badge--{{$package->status?'success':'danger'}}">
                                        {{$package->status? trans('Active'): trans('Inactive')}}
                                    </span>
                                </td>

                                <td data-label="@lang('Action')">
                                    <a href="javascript:void(0)" data-id="{{ $package->id }}" data-package="{{ $package }}" class="icon-btn edit-btn {{ $package->status==0?'disabled':'' }}" data-toggle="tooltip" data-placement="top" title="@lang('Edit')"><i class="la la-pencil"></i></a>
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


    <div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Package')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.package.store', 0) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" placeholder="@lang('Enter Package Name')" name="name" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Price')</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ $general->cur_sym }}</span>
                                </div>
                                <input type="text" class="form-control" placeholder="@lang('Enter Package Price')" name="price" />

                            </div>
                        </div>


                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="@lang('Enter Time Limit')" name="time_limit"/>
                            <div class="input-group-append">
                                <select class="input-group-text" name="unit">
                                    <option value="1">@lang('Days')</option>
                                    <option value="2" selected >@lang('Months')</option>
                                    <option value="3">@lang('Year')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Status')</label>
                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" name="status" checked>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn--primary">@lang('Add')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Package')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.package.store', 0) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" placeholder="@lang('Enter Package Name')" name="name" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Price')</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ $general->cur_sym }}</span>
                                </div>

                                <input type="text" class="form-control" placeholder="@lang('Enter Package Price')" name="price"/>
                            </div>
                        </div>


                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="@lang('Enter Time Limit')" name="time_limit"/>
                            <div class="input-group-append">
                                <select class="input-group-text" name="unit">
                                    <option value="1">@lang('Days')</option>
                                    <option value="2" selected >@lang('Months')</option>
                                    <option value="3">@lang('Year')</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Status')</label>
                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" name="status" checked>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn--primary">@lang('Update')</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <button data-toggle="modal" data-target="#addModal" class="btn btn--success mr-1 mb-2 mb-xl-0">
    <i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        'use strict';
        (function($){
            $(document).on('click', '.edit-btn', function () {
                var modal = $('#editModal');
                var data  = $(this).data('package');
                var link  = `{{ route('admin.package.store', '') }}/${data.id}`;
                modal.find('input[name=name]').val(data.name);
                modal.find('input[name=price]').val(data.price);
                modal.find('input[name=time_limit]').val(data.time_limit);
                modal.find('select[name=unit]').val(data.unit);

                if(data.status == 0){
                    modal.find('.toggle').addClass('btn--danger off').removeClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',false);

                }else{
                    modal.find('.toggle').removeClass('btn--danger off').addClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',true);
                }

                modal.find('form').attr('action', link);
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush


