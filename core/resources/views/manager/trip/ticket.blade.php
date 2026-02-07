<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ getImage('assets/images/logoIcon/favicon.png') }}">
    <title>{{ gs()->siteName($pageTitle) }}</title>
    <style>
        body {
            background-color: #eee;
            font-family: "Quicksand", sans-serif;
        }

        body p {
            text-align: center;
        }

        .w_ticket {
            position: relative;
            width: 780px;
            height: 440px;
            overflow: hidden;
            margin: auto;
        }

        .w_ticket .ticket {
            margin: auto;
            background-color: #31124b;
            width: 748px;
            height: 440px;
        }

        .w_ticket .ticket .back .corner {
            position: absolute;
            width: 32px;
            height: 32px;
            border-radius: 32px;
            border: 3px solid #31124b;
        }

        .w_ticket .ticket .back .cor_tl {
            top: -19px;
            left: -19px;
        }

        .w_ticket .ticket .back .cor_bl {
            bottom: -19px;
            left: -19px;
        }

        .w_ticket .ticket .back .cor_tr {
            top: -19px;
            right: -19px;
        }

        .w_ticket .ticket .back .cor_br {
            bottom: -19px;
            right: -19px;
        }

        .w_ticket .ticket .back .breakleft div,
        .w_ticket .ticket .back .breakright div {
            width: 12px;
            height: 12px;
            border-radius: 32px;
            border: 3px solid #31124b;
            margin-top: -3px;
        }

        .w_ticket .ticket .back .breakleft {
            position: absolute;
            top: 19px;
            left: -9px;
        }

        .w_ticket .ticket .back .breakright {
            position: absolute;
            top: 19px;
            right: -9px;
        }

        .w_ticket .ticket .back .breakcoverleft div,
        .w_ticket .ticket .back .breakcoverright div {
            width: 0px;
            height: 0px;
            border-radius: 32px;
            border: 3px solid transparent;
            margin-top: 9px;
            z-index: 100000;
        }

        .w_ticket .ticket .back .breakcoverleft {
            position: absolute;
            top: 20px;
            left: 0px;
        }

        .w_ticket .ticket .back .breakcoverleft div {
            border-right: 3px solid #31124b;
        }

        .w_ticket .ticket .back .breakcoverright {
            position: absolute;
            top: 20px;
            right: 0px;
        }

        .w_ticket .ticket .back .breakcoverright div {
            border-left: 3px solid #31124b;
        }

        .w_ticket .ticket .back .coverleft {
            position: absolute;
            top: 8px;
            left: -2px;
            bottom: 8px;
            border: 8px solid transparent;
            border-right: 12px solid #31124b;
        }

        .w_ticket .ticket .back .coverright {
            position: absolute;
            top: 8px;
            right: -2px;
            bottom: 8px;
            border: 8px solid transparent;
            border-left: 12px solid #31124b;
        }

        .w_ticket .ticket .front .rim {
            position: relative;
            top: 12px;
            width: 744px;
            height: 404px;
            border: 3px solid #fff;
            border-radius: 9px;
            display: flex;
            justify-content: center;
        }

        .w_ticket .ticket .front .rim .number {
            border-right: 3px solid #fff;
        }

        .w_ticket .ticket .front .rim .number:last-child {
            border-right: 0px;
        }

        .w_ticket .ticket .front .rim .number:first-child {
            border-right: 3px solid #fff;
        }

        .w_ticket .ticket .front .rim .number {
            position: relative;
            width: 30px;
            text-align: center;
        }

        .w_ticket .ticket .front .rim .number>span {
            position: relative;
            top: 50%;
            left: -174%;
            font-size: 20px;
            font-family: "Cutive Mono", Monospaced;
            color: #fff;
            display: block;
            width: 100px;
        }

        .w_ticket .ticket .front .rim .number.right>span {
            left: -72%;
        }

        .w_ticket .ticket .front .rim div:last-child {
            border-right: 0px;
        }

        .w_ticket .ticket .front .rim .number {
            position: relative;
            width: 30px;
            text-align: center;
        }

        .w_ticket .ticket .front .rim .number>span {
            position: relative;
            top: 50%;
            left: -174%;
            font-size: 20px;
            font-family: "Cutive Mono", Monospaced;
            color: #fff;
            transform: rotate(-90deg);
            display: block;
            width: 100px;
        }

        #print-ticket .title {
            text-align: center;
            color: #fff;
        }

        ul {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .justify-content-center {
            justify-content: center;
        }

        li {
            color: #fff;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .card {
            padding: 0 30px;
            border-right: 3px solid #fff !important;
        }

        .cmn-btn {
            position: relative;
            background: #fa9e1b;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            z-index: 2;
            overflow: hidden;
            -webkit-transition: all ease 0.5s;
            -moz-transition: all ease 0.5s;
            transition: all ease 0.5s;
            outline: none;
            box-shadow: none;
            border: none;
            margin-top: 20px;
            cursor: pointer;
        }

        .print-btn {
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="block1">
        <div class="w_ticket">
            <div class="ticket">
                <span class="back">
                    <div class="corner cor_tl"></div>
                    <div class="corner cor_bl"></div>
                    <div class="corner cor_tr"></div>
                    <div class="corner cor_br"></div>
                    <div class="breakleft">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="breakcoverleft">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="breakright">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="breakcoverright">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="coverleft"></div>
                    <div class="coverright"></div>
                </span>
                <span class="front">
                    <div class="rim">
                        <div class="number">
                            <span>
                                <span>{{ $ticket->trip->owner->general_settings->company_name }}</span>
                            </span>
                        </div>
                        <div class="card ">
                            <div class="card-body d-flex justify-content-center">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="print-ticket">
                                            <h4 class="title">{{ $ticket->trip->title }}</h4>
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Date of Journey')
                                                    <span>{{ showDateTime($ticket->date_of_journey, 'F d, Y') }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Departure  Time')
                                                    <span>{{ showDateTime($ticket->trip->schedule->starts_from, 'H:i A') }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Route')
                                                    <span>{{ $ticket->trip->route->name }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Pickup Point')
                                                    <span>{{ $ticket->passenger_details['from'] }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Dropping Point')
                                                    <span>{{ $ticket->passenger_details['to'] }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Passenger Name')
                                                    <span>{{ $ticket->passenger_details['name'] }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Gender')
                                                    <span>{{ showGender($ticket->passenger_details['gender']) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    @lang('Seats')
                                                    <span>
                                                        @foreach ($ticket->seats as $item)
                                                            {{ $item }}@if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="number right">
                            <span>
                                <span>{{ $ticket->trip->owner->general_settings->company_name }}</span>
                            </span>
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </div>

    <div class="print-btn">
        <button type="button" class="cmn-btn downloadBtn" id="demo">@lang('Print Ticket')</button>
    </div>

    @php
        $fileName = slug($ticket->passenger_details['name']) . '_' . time();
    @endphp

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/html2pdf.bundle.min.js') }}"></script>
    <script>
        "use strict";
        const options = {
            margin: 0.3,
            filename: `{{ $fileName }}`,
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'A4',
                orientation: 'landscape'
            }
        }

        var objstr = document.getElementById('block1').innerHTML;
        var strr = objstr;
        $('.downloadBtn').on('click', function(e) {
            e.preventDefault();
            var element = document.getElementById('demo');
            html2pdf().from(strr).set(options).save();
        });
    </script>
</body>

</html>
