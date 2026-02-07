@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            {{-- Quick Stats Summary --}}
            @php
                $avgRating = $ratings->avg('rating');
                $totalReviews = $ratings->total();
                $fiveStarCount = $ratings->where('rating', 5)->count();
                $fourStarCount = $ratings->where('rating', 4)->count();
                $lowRatingCount = $ratings->where('rating', '<=', 2)->count();
            @endphp

            <div class="row mb-3">
                <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">@lang('Average Rating')</h6>
                            <h3 class="mb-0">
                                <i class="las la-star text--warning"></i>
                                {{ $totalReviews > 0 ? number_format($avgRating, 1) : 'N/A' }}
                                <small class="text-muted">/5.0</small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">@lang('Total Reviews')</h6>
                            <h3 class="mb-0 text--info">{{ $totalReviews }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">@lang('5-Star Reviews')</h6>
                            <h3 class="mb-0 text--success">
                                {{ $fiveStarCount }}
                                @if($totalReviews > 0)
                                    <small class="text-muted">({{ number_format(($fiveStarCount/$totalReviews)*100, 0) }}%)</small>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">@lang('Low Ratings') (≤2★)</h6>
                            <h3 class="mb-0 {{ $lowRatingCount > 0 ? 'text--danger' : 'text--success' }}">
                                {{ $lowRatingCount }}
                                @if($lowRatingCount > 0)
                                    <i class="las la-exclamation-triangle"></i>
                                @else
                                    <i class="las la-check-circle"></i>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Passenger')</th>
                                    <th>@lang('Trip')</th>
                                    <th>@lang('Rating')</th>
                                    <th>@lang('Comment')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratings as $item)
                                    <tr>
                                        <td>{{ showDateTime($item->created_at, 'M d, Y') }}</td>
                                        <td>
                                            {{ $item->passenger->firstname }} {{ $item->passenger->lastname }}
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
                                            ({{ $item->rating }}/5)
                                        </td>
                                        <td>{{ $item->comment ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No feedback received yet')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($ratings->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ratings) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
