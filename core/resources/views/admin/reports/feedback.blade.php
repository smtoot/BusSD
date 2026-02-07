@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Comment')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $item)
                                    <tr>
                                        <td>{{ showDateTime($item->created_at, 'M d, Y') }}</td>
                                        <td>
                                            {{ $item->passenger->firstname }} {{ $item->passenger->lastname }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.detail', $item->trip->owner_id) }}">{{ $item->trip->owner->fullname }}</a>
                                        </td>
                                        <td>{{ $item->trip->title }}</td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $item->rating)
                                                    <i class="las la-star text--warning"></i>
                                                @else
                                                    <i class="lar la-star text--warning"></i>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>{{ $item->comment ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No feedback recorded yet')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($feedbacks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($feedbacks) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
