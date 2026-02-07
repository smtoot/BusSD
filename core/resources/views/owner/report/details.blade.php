@extends('owner.layouts.app')
@section('panel')
    <div class="row d-flex justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Ticket Number')</span>
                            <span class="text--danger fw-bolder">{{ sprintf('%06d', $sale->id) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Booked at')</span>
                            <span>{{ showDateTime($sale->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Date of Journey')</span>
                            <span class="fw-bold ">{{ showDateTime($sale->date_of_journey, 'M d, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Passenger Name')</span>
                            <span class="text--deep-purple fw-bold">
                                {{ $sale->passenger_details['name'] }}
                                ({{ showGender($sale->passenger_details['gender']) }})
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Passenger E-mail')</span>
                            <span>{{ @$sale->passenger_details['email'] }}</span>
                        </li>
                        @if (isset($sale->passenger_details['mobile_number']) && $sale->passenger_details['mobile_number'] != '')
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">@lang('Passenger Mobile No.')</span>
                                <span>{{ $sale->passenger_details['mobile_number'] }}</span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Pickup Point')</span>
                            <span>{{ @$sale->passenger_details['from'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Dropping Point')</span>
                            <span>{{ @$sale->passenger_details['to'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Booked Seats')</span>
                            <span>
                                @foreach ($sale->seats as $item)
                                    <span class="bg--10 px-2 py-1 rounded-pill">{{ $item }}</span>
                                @endforeach
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Trip')</span>
                            <span class=" font-italic">{{ $sale->trip->title }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Price/Ticket')</span>
                            <span class="fw-bold text--cyan">
                                {{ $owner->general_settings->cur_sym }}{{ showAmount($sale->price, currencyFormat: false) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Number of Ticket')</span>
                            <span class="fw-bold text--cyan">{{ $sale->ticket_count }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Booked By')</span>
                            <span class="text--deep-purple fw-bold"> {{ $sale->counterManager->fullname }} </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Total Amount')</span>
                            <span class="fw-bold text--danger">
                                {{ $owner->general_settings->cur_sym }}{{ showAmount($sale->ticket_count * $sale->price, currencyFormat: false) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('owner.report.sale.index') }}" />
@endpush
