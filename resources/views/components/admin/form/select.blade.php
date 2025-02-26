@props(['required' => false, 'placeholder' => null, 'class' => ''])


<select {{ $required ? 'required' : '' }}  class="block w-full p-2.5 border rounded-lg sm:text-sm text-gray-900 dark:text-gray-50 bg-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-700 focus:border-gray-300 focus:ring-2 focus:ring-primary-900 dark:focus:ring-primary-700 {{$class}}" {!! $attributes !!}>
    @if (isset($placeholder))
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
