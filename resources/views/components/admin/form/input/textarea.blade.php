@props(['class', 'error', 'name', 'id', 'minlength', 'maxlength', 'min', 'max', 'step', 'placeholder', 'required', 'readonly', 'disabled', 'value', 'rows'])

<textarea
    {!! $attributes !!}
    class="focus:ring-2 dark:focus:ring-2 focus:ring-primary-900 dark:focus:ring-primary-700 block w-full resize-none rounded-lg border border-gray-300 bg-gray-100 p-2.5 text-gray-900 placeholder-gray-500 focus:border-transparent dark:border-gray-700 dark:bg-gray-500 dark:text-gray-50 dark:placeholder-gray-400  sm:text-sm disabled:cursor-not-allowed disabled:text-gray-600 disabled:dark:text-gray-400 disabled:bg-gray-200 disabled:dark:bg-gray-600 {{ $class }} {{ $error ? 'ring-2 ring-red-600' : '' }}"
    {{ $name ? "name=$name" : '' }}
    {{ $id ? "id=$id" : '' }}
    {{ $min ? "minlength=$min" : '' }}
    {{ $max ? "maxlength=$max" : '' }}
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $readonly ? 'readonly' : '' }}
    {{ $disabled ? 'disabled' : '' }}>{{ $value }}</textarea>