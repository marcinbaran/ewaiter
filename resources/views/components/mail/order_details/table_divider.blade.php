@props(['color' => 'light-grey'])

@php
    $color = match ($color) {
        'white' => '#f5f5f5',
        'black' => '#262828',
        'light-grey' => '#dddddd',
        'light-pink' => '#f1acb7',
        'pink' => '#ec3f59',
        default => '#dddddd',
    };
@endphp

<tr>
    <td colspan="3" style="height: 1px; background-color: {{$color}}"></td>
</tr>
