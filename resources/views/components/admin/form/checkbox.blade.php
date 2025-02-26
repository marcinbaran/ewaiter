@props([
    'name' => null,
    'disabled' => false,
    'checked' => false,
    'uncheckedValue' => 0,
    'checkedValue' => 1
    ])

<input name="{{ $name ?? '' }}" type="hidden" value="{{ $uncheckedValue }}">
<input name="{{ $name ?? '' }}" {{ $disabled ? 'disabled' : '' }} {{ $value == $checked ? 'checked':'' }} type="checkbox" value="{{ $checkedValue }}"
{!! $attributes->merge(['class' => 'w-4 h-4 text-primary-700 bg-gray-100 border-gray-300 rounded focus:ring-primary-300 dark:focus:ring-primary-500 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!} >
