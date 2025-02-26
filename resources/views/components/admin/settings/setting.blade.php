@props(['name', 'key', 'route' => '#'])

@php
    $currentRoute = request()->getRequestUri();
    $passedRoute = strstr($route, '/admin');
    $isActive = $currentRoute == $passedRoute;

@endphp
<div class="overflow-visible py-1 md:py-0">
    <a class="{{ $isActive ? 'text-primary-900 hover:text-primary-500 dark:text-primary-700 dark:hover:text-primary-700 md:-translate-x-2' : 'text-gray-600 hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-50' }} group flex h-10 transform flex-row items-center justify-between gap-2 overflow-hidden p-2 py-1 text-lg transition-all duration-100 ease-in-out md:py-0 md:text-sm md:hover:-translate-x-2"
       href="{{ $route }}">
        <div class="flex flex-row gap-2">
            <div class="[&>*:first-child]:h-6 [&>*:first-child]:w-6 [&>*:first-child]:stroke-[1.5]">
                {!! config('icons.' . $key) ?? '' !!}
            </div>
            <div class="flex items-start justify-start">
                {{ __('settings.' . $name) }}
            </div>
        </div>
        <svg
            class="{{ $isActive ? 'translate-x-0' : 'translate-x-full' }} icon icon-tabler icon-tabler-chevron-left group-hover:translate-x-0"
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M15 6l-6 6l6 6"></path>
        </svg>
    </a>
</div>
