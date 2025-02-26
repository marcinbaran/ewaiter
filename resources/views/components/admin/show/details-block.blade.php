@props(['title' => ''])

<div  {!! $attributes->merge(['class' => 'border sm:text-sm rounded-lg p-4 text-gray-900 bg-gray-100 border-gray-300 dark:text-gray-50 dark:bg-gray-800 dark:border-gray-700 ' ]) !!} >
    @if($title)
        <h3 class="mb-2 text-xl font-semibold">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
