@props(['index', 'name', 'additions', 'price', 'currency'])

<tr>
    <td style="width: 1px; padding-right: 10px; padding: 10px 10px 10px 0">
        <div
            style="width: 20px; height: 20px; display: flex; justify-content: center; align-items: center; border: 1px solid #ec3f59; border-radius: 100%; color: #ec3f59; font-weight: 700;">
            {{$index}}
        </div>
    </td>
    <td style="padding: 10px 0;">
        <div style="font-size: 14px;">{{$name}}</div>
        <div style="color: #8e8e8e; font-weight: 300">{{$additions}}</div>
    </td>
    <td style="display: flex; justify-content: end; font-size: 14px; font-weight: 300; padding: 10px 0;">
        {{$price}} {{$currency}}
    </td>
</tr>
