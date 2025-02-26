<!DOCTYPE html>
<html>
<head>
    <title>{{ gtrans('emails.Reservation confirmation') }}</title>
</head>

<body>
<h2>{{ gtrans('emails.Reservation confirmation') }}</h2>
<br/>
<p><b>{{ gtrans('reservations.Table') }}:</b> {{$reservation['table']['number']}}. </p>
<p><b>{{ gtrans('reservations.Start') }}:</b> {{$reservation['start']}}.  </p>
<br/>
</body>

</html>