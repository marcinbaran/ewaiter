@props([
    'name' => '',
    'id' => '',
    'class' => '',
    'containerClass' => '',
    'value' => 1,
    'uncheckedValue' => 0,
    'placeholder' => '',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'error' => '',
])

<input type="hidden" class="tailwind-class-loader block ring-2 ring-primary-900 dark:ring-primary-700" />
<div
    class="new-input inline-flex p-0 text-gray-900 dark:text-gray-50 {{ $containerClass }}">
    <label class="relative inline-flex cursor-pointer items-center">
        <input {{ $name ? "name=$name" : '' }} type="hidden" value="{{ $uncheckedValue }}">
        <input {{ $name ? "name=$name" : '' }} {{ $disabled ? 'disabled' : '' }}  {{ $required ? 'required' : '' }} {{ $checked ? 'checked' : '' }} type="checkbox" {{ $value ? "value=$value" : '' }} {{ $id ? "id=$id" : '' }}
        class="w-4 h-4 text-primary-700 bg-gray-100 border-gray-300 rounded focus:ring-primary-300 dark:focus:ring-primary-500 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 peer sr-only {{ $class }}" {!! $attributes !!} />
        <div
            class="peer h-6 w-11 rounded-full border bg-gray-200 dark:bg-gray-600 border-gray-300 dark:border-gray-700 peer-focus:ring-primary-900 dark:peer-focus:ring-primary-700 peer-focus:ring-2 peer-checked:bg-primary-900 peer-checked:dark:bg-primary-700 peer-checked:after:translate-x-full after:absolute after:left-[2px] after:top-[50%] after:h-5 after:w-5 after:-translate-y-1/2 after:rounded-full after:border after:transition-all after:content-[''] after:bg-gray-50 after:border-gray-300 {{ $error ? 'ring-2 ring-red-600 peer-focus:ring-red-600 dark:peer-focus:ring-red-600 peer-checked:bg-red-500 peer-checked:dark:bg-red-500 ' : '' }} ">
        </div>
        <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400">{{ $placeholder }}</span>
    </label>
</div>