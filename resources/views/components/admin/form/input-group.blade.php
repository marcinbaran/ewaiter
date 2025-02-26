@props(['disabled' => false, 'required' => false, 'prepend' => '', 'append' => '', 'class' => ''])

@php
    if($prepend) {
        $class .= ' border-l-0 rounded-l-none';
    }
    if($append) {
        $class .= ' border-r-0 rounded-r-none';
    }
@endphp

<div class="flex">
    @if($prepend)
    <div class="flex whitespace-nowrap p-2.5 text-sm text-gray-900 bg-gray-200 border-gray-300 border border-r-0 rounded-l-md dark:text-gray-400 dark:bg-gray-700  dark:border-gray-600">{!! $prepend !!}</div>
    @endif
    <x-admin.form.input class="grow w-auto {{ $class }}" :attributes="$attributes" :disabled="$disabled" :required="$required" />
    @if($append)
    <div class="flex whitespace-nowrap p-2.5 text-sm text-gray-900 bg-gray-200 border-gray-300 border border-l-0 rounded-r-md dark:text-gray-400 dark:bg-gray-700  dark:border-gray-600">{!! $append !!}</div>
    @endif
</div>
