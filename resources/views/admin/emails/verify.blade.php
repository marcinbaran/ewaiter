<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>{{ gtrans('emails.Welcome Email') }}</title>
    <style>
        @media screen {
            @font-face {
                font-family: 'Montserrat';
                font-style: normal;
                font-weight: 400;
                font-display: swap;
                src: url(https://fonts.gstatic.com/s/montserrat/v15/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
                unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            }
            @font-face {
                font-family: 'Montserrat';
                font-style: normal;
                font-weight: 700;
                font-display: swap;
                src: url(https://fonts.gstatic.com/s/montserrat/v15/JTURjIg1_i6t8kCHKm45_dJE3gfD_u50.woff2) format('woff2');
                unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            }
        }
        body {
            font-family: 'Montserrat', sans-serif;
            color: white;
        }
        .link {
            color: white !important;
            text-decoration: none;
            line-height: 34px;
            font-size: 18px;
            font-family: 'Montserrat', sans-serif;

        }
        a.link {
            color: white !important;
        }
        .font-size-x15, .font-size-x20{
            font-weight: bold;

        }
        .font-size-x15{
            font-size: 20px;

        }
        .font-size-x20{
            font-size: 32px;
        }
        .secondBlockContainer {
            padding-top: 100px;
            text-align: center;
            height: 343px;
        }
        .secondBlockWrapper, .firstBlockWrapper {
            background-repeat: no-repeat !important;
            background-size: 100% !important;
            border-radius: 6px !important;
            margin: 10px;
            width: 710px;
        }
        .buttonPrimary{
            color: white;
            font-weight: 600;
            background: red;
            color: white;
            text-decoration: none;
            border: 2px solid white;
            border-radius: 22px;
            padding: 12px 40px;
        }
        .mt-20{
            padding-top: 20px;
        }
        .footer {
            background: #9ab420 !important;
            text-align: center;
            color: white;
            width: 720px;
            padding: 5px;
            border-radius: 5px;
        }
        .footer a, .secondBlockContainer p, .secondBlockContainer p a{
            color: white;
            text-decoration: none;
        }
        .footer a, .secondBlockContainer p, .secondBlockContainer p a
        {
            color: white !important;
            text-decoration: none !important;
        }
        .mainWrapper{
            background: #ededed;
            padding-top: 5px;
        }
    </style>
</head>

<body>

<div style="width: 730px" class="mainWrapper">
    <div class="firstBlockWrapper" style="background:url('{{asset('/images/email/welcome_header.png')}}');height: 270px; background-repeat: no-repeat">
        <div style="text-align: center;">
            <a href="https://wirtualnykelner.pl" class="link">www.wirtualnykelner.pl</a>
        </div>
    </div>
    <div class="secondBlockWrapper" style="background:url('{{asset('/images/email/welcome_main.png')}}');">
        <div class="secondBlockContainer" >
            <p class="font-size-x15">ZA REJESTRACJĘ OTRZYMUJESZ</p>
            <p class="font-size-x20">DARMOWĄ GRĘ!</p>
            <a href="https://primebitstore.com/?p=3" class="buttonPrimary" style="color: white !important;">POBIERZ GRĘ</a>
            <p class="font-size-x15 mt-20" style="color: white !important;">PRIMEBITSTORE.COM</p>
        </div>
    </div>
    <div class="footer">
        <p>E-Waiter 2020 All Rights Reserved</p>
        <p><a href="https://wirtualnykelner.pl/static/media/regulamin_ewaiter.9fdbf435.pdf">Regulamin</a> i <a href="https://wirtualnykelner.pl/static/media/polityka_prywatnosci_ewaiter.1cd2dbf5.pdf">Polityka prywatności</a></p>
    </div>
</div>
</body>

</html>