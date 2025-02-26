@props(['disabled' => false, 'required' => false, 'readonly' => false, 'class' => '', 'error' => ''])

<input
    {!! $attributes !!}
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $readonly ? 'readonly' : '' }}
    type="{{ $attributes['type'] ?? 'text' }}"
    class="w-full p-2.5 border sm:text-sm rounded-lg block text-gray-900 bg-gray-100 placeholder-gray-600 border-gray-300 focus:border-gray-300 focus:ring-2 focus:ring-primary-900 dark:text-gray-50 dark:bg-gray-600 dark:border-gray-700 dark:placeholder-gray-400 dark:focus:ring-2 dark:focus:ring-primary-700 disabled:opacity-75 disabled:cursor-not-allowed {{ $class }} {{ $error ? 'ring-2 ring-red-600' : ''}}"
>