@props(['color' => 'white', 'text', 'url'])

@php
    $textColor = match ($color) {
        'white' => 'text-light-pink',
        'light-pink' => 'text-black',
        default => 'text-light-pink',
    };
    $backgroundColor = match ($color) {
        'white' => 'bg-white',
        'light-pink' => 'bg-light-pink',
        default => 'bg-white',
    };
    $borderColor = match ($color) {
        'white' => 'border-px-solid-light-pink',
        'light-pink' => 'border-px-solid-light-pink',
        default => 'border-px-solid-light-pink',
    };
@endphp

<mj-button mj-class="button {{$textColor}} {{$backgroundColor}} {{$borderColor}}"
           href="{{$url}}">
    {{$text}}
</mj-button>
