<!DOCTYPE html>
<html>
<head>
    <title>{{ gtrans('emails.Order has been canceled') }}</title>
</head>

<body>
<h2>{{ gtrans('emails.Order has been canceled') }}</h2>
<br/>
<p><b>{{ gtrans('admin.Restaurant') }} {{$restaurant['name']}}</b>. </p>
<p><b>{{ gtrans('admin.Bill') }} {{$bill['id']}}</b>. </p>
<p><b>{{ gtrans('admin.Created at') }}:</b> {{$bill['created_at']}}. </p>
<br/>
</body>

</html>