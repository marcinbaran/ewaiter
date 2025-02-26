@props(['disabled' => false, 'resize' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'block p-2.5 w-full text-sm rounded-lg border text-gray-900 bg-gray-100 placeholder-gray-600 border-gray-300 focus:border-gray-300 focus:ring-2 focus:ring-primary-900 dark:text-gray-50 dark:bg-gray-600 dark:border-gray-700 dark:placeholder-gray-400 dark:focus:ring-2 dark:focus:ring-primary-700 disabled:opacity-75 disabled:cursor-not-allowed '.(!$resize
            ? 'resize-none'
            : '').' ',
    'rows' => '4',
]) !!}>{!! $slot !!}</textarea>
