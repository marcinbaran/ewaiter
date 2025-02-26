@props(['color', 'type' => 'button', 'wireClick' => ''])

@php
    $colorClasses = 'text-sm focus:ring-2 font-medium rounded-lg text-gray-50 hover:underline dark:text-gray-50 px-5 py-2 flex justify-center items-center';

    $colorClasses .= $color != 'link' ? ' text-gray-50 ':' text-gray-900 ';

    switch($color) {
        case 'link':
            $colorClasses .= 'dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700
            dark:hover:border-gray-600 dark:focus:ring-gray-700';
            break;
        case 'danger':
            $colorClasses .= ' bg-red-700 hover:bg-red-800 focus:ring-red-300 dark:bg-red-600
            dark:hover:bg-red-700 dark:focus:ring-red-900 ';
            break;
        case 'primary':
            $colorClasses .= ' bg-primary-900 hover:bg-primary-800 focus:ring-primary-700 dark:bg-primary-700
            dark:hover:bg-primary-800 dark:focus:ring-primary-900 ';
            break;
        case 'success':
            $colorClasses .= ' bg-primary-900 hover:bg-primary-800 focus:ring-primary-700 dark:bg-primary-700
            dark:hover:bg-primary-800 dark:focus:ring-primary-900 ';
            break;
        case 'warning':
            $colorClasses .= ' bg-yellow-400 hover:bg-yellow-500 focus:ring-yellow-300 dark:focus:ring-yellow-900 ';
            break;
        case 'dark':
            $colorClasses .= ' bg-gray-800 hover:bg-gray-900 focus:ring-gray-300 dark:bg-gray-800
            dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 ';
            break;
        case 'alternative':
            $colorClasses .= ' bg-purple-700 hover:bg-purple-800  focus:ring-purple-300 dark:bg-purple-600
            dark:hover:bg-purple-700 dark:focus:ring-purple-900 ';
            break;
        case 'cancel':
            $colorClasses .= ' bg-gray-600 hover:bg-gray-500 focus:ring-gray-400 dark:bg-gray-700
            dark:hover:bg-gray-600 dark:focus:ring-gray-900 ';
            break;
        case 'print':
            $colorClasses .= ' bg-blue-700 hover:bg-blue-800  focus:ring-blue-300 dark:bg-blue-600
            dark:hover:bg-blue-700 dark:focus:ring-blue-900 ';
            break;

    }
@endphp

@if($type == 'link' || $type == 'cancel')
    <a {{ $attributes->merge(['href' => '#', 'class' => $colorClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $colorClasses]) }}>
        {{ $slot }}
    </button>
@endif
