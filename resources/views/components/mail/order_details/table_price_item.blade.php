@props(['title', 'price', 'currency'])

<tr>
    <td style="font-size: 14px; padding: 10px 0;">{{$title}}</td>
    <td style="font-size: 14px; font-weight: 300; text-align: right; padding: 10px 0;">
        {{$price}} {{$currency}}
    </td>
</tr>
