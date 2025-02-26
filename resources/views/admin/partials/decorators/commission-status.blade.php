@props(['classes' => '', 'text' => '', 'styles' => ''])

<span class=" {{ $classes }} text-sm font-semibold inline-flex items-center p-1.5 rounded-full" style="{{$styles}}">
    {{ $text }}
</span>
