<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('pdf.report')}}</title>
    <style>
        @page {
            margin-top: 3rem;
            margin-bottom: 4rem;
        }

        * {
            box-sizing: border-box;
        }

        body {
            padding-top: 4rem;
            padding-bottom: 5rem;
            font-family: "DejaVu Serif", serif;
            font-size: 12px;
            color: #505050;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        p {
            margin: 0.25rem 0;
        }

        span.status {
            font-weight: 700;
        }

        span.status--new {
            color: #a855f7;
        }

        span.status--accepted {
            color: #6366f1;
        }

        span.status--ready {
            color: #10b981;
        }

        span.status--canceled {
            color: #ef4444;
        }

        span.status--complaint {
            color: #eab308;
        }

        span.status--released {
            color: #202020;
        }

        img {
            display: block;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3rem;
        }

        header > img {
            position: absolute;
            top: 50%;
            height: 100%;
            transform: translateY(-50%);
        }

        header > img.e-waiter {
            height: 50%;
            left: 0;
        }

        header > img.mensa-magna {
            right: 0;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4rem;
            border-top: 1px solid #a6a6a8;
        }

        footer p {
            margin: 0;
        }

        footer .informations__column {
            position: absolute;
            top: 1rem;
            left: 0.5%;
            width: 33.33%;
            height: 100%;
        }

        footer .information-span {
            padding-top: 0.4rem;
            width: 100%;
            position: relative;
        }

        footer .information-span__icon {
            position: absolute;
            top: 0;
            left: 0;
            width: 1rem;
        }

        footer .information-span__text {
            position: absolute;
            top: 0;
            left: 1.5rem;
            width: 90%;
        }

        footer .informations__column:nth-child(1) img.mensa-magna {
            height: 3rem;
        }

        footer .informations__column:nth-child(2) {
            left: 38%;
        }

        footer .informations__column:nth-child(3) {
            width: 21%;
            right: 0;
            left: auto;
        }

        footer .informations__column:nth-child(3) .information-span:nth-child(2) {
            top: 1.5rem;
        }

        main .informations__column {
            width: 50%;
            float: left;
        }

        main .informations__column:nth-child(2) {
            text-align: right;
        }

        main table {
            margin-top: 2rem;
            width: 100%;
            font-size: 0.6rem;
            border: 1px solid #a6a6a8;
            border-collapse: collapse;
        }

        main table th {
            font-size: 0.8rem;
            background-color: #cccccc;
        }

        main table th,
        main table td {
            border: 1px solid #a6a6a8;
            padding: 0.1rem 0.25rem;
        }

        main table tr.no-provision {
            background-color: #f2f2f2;
        }

        main table tr:last-child {
            background-color: #cccccc;
        }

        main table tr:last-child > td:first-child {
            font-weight: 700;
            text-align: right;
        }

        main h2 {
            text-align: right;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
<header class="clearfix">
    <img class="e-waiter" src="{{ asset('images/pdf/e-waiter_pink.png') }}" alt="{{__('pdf.e-waiter_logo')}}">
    <img class="mensa-magna" src="{{ asset('images/pdf/mensa-magna.png') }}" alt="{{__('pdf.mensa-magna_logo')}}">
</header>
<footer class="clearfix">
    <div class="informations__column">
        <img class="mensa-magna" src="{{ asset('images/pdf/mensa-magna.png') }}"
             alt="{{__('pdf.mensa-magna_logo')}}">
    </div>
    <div class="informations__column clearfix">
        <div class="information-span">
            <img class="information-span__icon" src="{{ asset('images/pdf/map-pin.png') }}"
                 alt="{{__('pdf.map-pin-icon')}}">
            <div class="information-span__text">
                <p>{{ strtolower(__('pdf._abbreviation.street')) }} Stanisława Moniuszki 8</p>
                <p>35-015 Rzeszów</p>
            </div>
        </div>
    </div>
    <div class="informations__column">
        <div class="information-span">
            <img class="information-span__icon" src="{{ asset('images/pdf/mail.png') }}"
                 alt="{{__('pdf.mail-icon')}}">
            <div class="information-span__text">
                <p>kontakt@e-waiter.pl</p>
            </div>
        </div>
        <div class="information-span">
            <img class="information-span__icon" src="{{ asset('images/pdf/info-circle.png') }}"
                 alt="{{__('pdf.info-icon')}}">
            <div class="information-span__text">
                <p>NIP 8133887101</p>
                <p>KRS 0000997250</p>
            </div>
        </div>
    </div>
</footer>
<main>

    <h1>{{ __('pdf.report') }} {{ strtolower(__('pdf._abbreviation.number')) }} {{ $reportNumber }}</h1>
    <div class="informations clearfix">
        <div class="informations__column">
            <p>{{ __('pdf.time-span') }}: {{ $timeSpanStartDate }} - {{ $timeSpanEndDate }}</p>
            <p>{{ __('pdf.generate-date') }}: {{ $generateAt }}</p>
            <p>{{ __('pdf.bills') }}: {{ $bills->count() }}</p>
            <p>{{ __('pdf.provision') }}: {{ $restaurant['provision'] }}%</p>
        </div>
        <div class="informations__column">
            <p>{{ $restaurant['name'] }}</p>
            <p>{{ strtolower(__('pdf._abbreviation.street')) }} {{ $restaurant['address']->street }} {{ $restaurant['address']->house_number ? $restaurant['address']->building_number.'/'.$restaurant['address']->house_number : $restaurant['address']->building_number }}</p>
            <p>{{ $restaurant['address']->postcode }} {{ $restaurant['address']->city }}</p>
        </div>
    </div>
    <table>
        <tr>
            <th>{{ __('pdf._abbreviation.ordinal-number') }}</th>
            <th>{{ __('pdf.bill-number') }}</th>
            <th>{{ __('pdf.date') }}</th>
            <th>{{ __('pdf.payment') }}</th>
            <th>{{ __('pdf.delivery') }}</th>
            <th>{{ __('pdf.status') }}</th>
            {{--            <th>{{ __('pdf.service-charge') }}</th>--}}
            {{--            <th>{{ __('pdf.package') }}</th>--}}
            <th>{{ __('pdf.amount') }}</th>
            <th>{{ __('pdf.delivery') }}</th>
            <th>{{ __('pdf.points') }}</th>
            <th>{{ __('pdf.provision') }}</th>
        </tr>
        @foreach($bills as $bill)
            <tr class="{{ $bill['status'] === App\Models\Bill::getStatusName(\App\Enum\OrderStatus::COMPLAINT->value) ? 'no-provision' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{$restaurant['code']}} {{ $bill['id'] }}</td>
                <td>{{ $bill['date'] }}</td>
                <td>{{ __('pdf._payment-type.'.$bill['paymentType']) }}</td>
                <td>{{ __('pdf._delivery-type.'.$bill['deliveryType']) }}</td>
                <td><span
                        class="status status--{{ $bill['status'] }}">{{ __('pdf._status.'.$bill['status']) }}</span>
                </td>
                {{--                <td>{{ $bill['serviceCharge'] }} {{ $bill['currency'] }}</td>--}}
                {{--                <td>{{ $bill['package'] }} {{ $bill['currency'] }}</td>--}}
                <td>{{ $bill['amount'] }} {{ $bill['currency'] }}</td>
                <td>{{ $bill['delivery'] }} {{ $bill['currency'] }}</td>
                <td>{{ $bill['points'] }} {{ $bill['currency'] }}</td>
                <td>{{ !($bill['status'] === App\Models\Bill::getStatusName(\App\Enum\OrderStatus::COMPLAINT->value)) ? $bill['provision'].' '.$bill['currency'] : __('pdf._abbreviation.not-applicable') }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6">{{ __('pdf.total') }}:</td>
            {{--            <td>{{ $totals['serviceCharge'] }} {{ $bills[0]['currency'] ?? '' }}</td>--}}
            {{--            <td>{{ $totals['package'] }} {{ $bills[0]['currency'] ?? '' }}</td>--}}
            <td>{{ $totals['amount'] }} {{ $bills[0]['currency'] ?? '' }}</td>
            <td>{{ $totals['delivery'] }} {{ $bills[0]['currency'] ?? '' }}</td>
            <td>{{ $totals['points'] }} {{ $bills[0]['currency'] ?? '' }}</td>
            <td>{{ $totals['provision'] }} {{ $bills[0]['currency'] ?? '' }}</td>
        </tr>
    </table>
    <h2>{{ __('pdf.total-sales') }}: {{ $totalSales }} {{ $bills[0]['currency'] ?? '' }}</h2>
</main>
</body>
</html>
