@props([
    'disabled' => false,
    'required' => false,
    'class' => '',
    'containerClass' => '',
    'error' => '',
    'name' => '',
    'id' => '',
    'placeholder' => '',
    'value' => '',
    'oldValue' => '',
    'mode' => 'single',
    'nullOption' => '',
])

<div class="new-input-parent w-full h-full flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : '' }}">
    <div
        class="new-input w-full h-full flex-grow p-0 inline-flex sm:text-sm border rounded-lg text-gray-900 dark:text-gray-50 bg-gray-100 dark:bg-gray-500 border-gray-300 dark:border-gray-700 overflow-hidden {{ $containerClass }}">
        <select
            class="select2 flex-grow p-2.5 text-inherit bg-inherit placeholder-gray-500 dark:placeholder-gray-400 border-none focus:ring-0 focus:border-transparent disabled:text-gray-500 disabled:dark:text-gray-400 disabled:bg-gray-200 disabled:dark:bg-gray-600 disabled:cursor-not-allowed {{ $class }}"
            {{ $name ? "name=$name" : '' }} {{ $id ? "id=$id" : '' }} {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }} {{ $mode ? "data-select2-mode=$mode" : '' }}
            {{ $value ? "data-select2-url=$value" : '' }} data-select2-old-value="{{$oldValue}}"
            data-select2-null-option="{{$nullOption}}"
            {!! $attributes !!}>
            @if ($placeholder != '')
                <option value="blank" selected>{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>
    </div>
</div>
