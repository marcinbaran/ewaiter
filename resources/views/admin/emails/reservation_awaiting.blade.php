<!DOCTYPE html>
<html>
<head>
    <title>{{ gtrans('emails.Reservation awaiting') }}</title>
</head>

<body>
<h2>{{ gtrans('emails.Reservation awaiting') }}</h2>
<br/>
<p><b>{{ gtrans('reservations.Table') }}:</b> {{$reservation['table']['number']}}. </p>
<p><b>{{ gtrans('reservations.Start') }}:</b> {{$reservation['start']}}.  </p>
<p><b>Link: </b><a href="{{Route('admin.reservations.show',['reservation'=>$reservation['id']])}}">LINK</a>  </p>
<br/>
</body>

</html>