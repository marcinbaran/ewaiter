@props(['value', 'name' => 'weekdays', 'id' => '', 'class' => '', 'checked' => false])
<div class="daypicker relative {{$class}}">
    <input class="hidden" name="{{ $name }}" type="text" value="0">
    <input class="absolute top-0 left-0 -z-10 opacity-0" id="{{ $id }}" name="{{ $name }}" type="checkbox" value="1" {{ $checked ? 'checked' : '' }} />
    <label for="{{ $id }}" class="block w-full h-full rounded-lg px-3 py-2 select-none cursor-pointer uppercase text-sm text-center font-bold border text-gray-600 bg-gray-200 border-gray-300 dark:text-gray-400 dark:bg-gray-600 dark:border-gray-700 [&.active]:text-gray-50 [&.active]:bg-primary-900 [&.active]:border-primary-900 dark:[&.active]:text-gray-50 dark:[&.active]:bg-primary-700 dark:[&.active]:border-primary-700" {!! $attributes !!}>
        {{ $value }}
    </label>
</div>
