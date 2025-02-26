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

<mj-divider border-width="1px" border-style="solid" border-color="{{$color}}" />
