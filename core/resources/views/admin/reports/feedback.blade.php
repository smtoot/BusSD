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
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Trip / Operator')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Comment')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $feedback)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$feedback->passenger->fullname }}</span>
                                            <br>
                                            <small><a href="{{ route('admin.passengers.show', @$feedback->passenger_id) }}">@lang('View Profile')</a></small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$feedback->trip->title }}</span>
                                            <br>
                                            <small>{{ @$feedback->trip->owner->fullname }}</small>
                                        </td>
                                        <td>
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $feedback->rating)
                                                    <i class="las la-star text--warning"></i>
                                                @else
                                                    <i class="lar la-star text--warning"></i>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>{{ $feedback->review }}</td>
                                        <td>{{ showDateTime($feedback->created_at) }}</td>
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
                @if ($feedbacks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($feedbacks) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
