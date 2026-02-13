@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Total App Sales')</th>
                                    <th>@lang('Total Commission')</th>
                                    <th>@lang('Total Payouts')</th>
                                    <th>@lang('Current Balance')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($owners as $owner)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $owner->fullname }}</span>
                                            <br>
                                            <small> <a href="{{ route('admin.users.detail', $owner->id) }}"><span>@</span>{{ $owner->username }}</a> </small>
                                        </td>
                                        <td>
                                            {{ gs('cur_sym') }}{{ showAmount($owner->total_operator_earnings + $owner->total_commission) }}
                                        </td>
                                        <td>
                                            <span class="text--danger">{{ gs('cur_sym') }}{{ showAmount($owner->total_commission) }}</span>
                                        </td>
                                        <td>
                                            {{ gs('cur_sym') }}{{ showAmount($owner->total_payouts) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($owner->balance) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $owner->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-desktop"></i> @lang('Details')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($owners->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($owners) }}
                </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection
