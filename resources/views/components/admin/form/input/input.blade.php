@props([
    'disabled' => false,
    'required' => false,
    'readonly' => false,
    'class' => '',
    'containerClass' => '',
    'error' => '',
    'name' => '',
    'id' => '',
    'minlength' => null,
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'minTime' => null,
    'format' => null,
    'formatMin' => null,
    'formatMax' => null,
    'step' => null,
    'placeholder' => '',
    'value' => '',
])

<input type="hidden" class="tailwind-class-loader block ring-2 ring-primary-900 dark:ring-primary-700" />
<div
    class="new-input min-w-0 p-0 flex sm:text-sm border rounded-lg text-gray-900 dark:text-gray-50 bg-gray-100 dark:bg-gray-500 border-gray-300 dark:border-gray-700 overflow-hidden {{ $error ? 'ring-2 ring-red-600' : '' }} {{ $containerClass }}">
    <input
        class="min-w-0 flex-1 p-2.5 text-inherit bg-inherit placeholder-gray-500 dark:placeholder-gray-400 border-none focus:ring-0 focus:border-transparent disabled:text-gray-500 disabled:dark:text-gray-400 disabled:bg-gray-200 disabled:dark:bg-gray-600 disabled:cursor-not-allowed {{ $class }}"
        type="{{ $attributes['type'] ?? 'text' }}" {{ $name ? "name=$name" : '' }}
        {{ $id ? "id=$id" : '' }} value="{{ $value }}" {{ $minlength != null ? "minlength=$minlength" : '' }}
        {{ $maxlength != null ? "maxlength=$maxlength" : '' }} {{ $min != null ? "min=$min" : '' }} {{ $max != null ? "max=$max" : '' }} {{ $minTime !== null ? "mintime=$minTime" : '' }}
        {{ $step ? "step=$step" : '' }} placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }} {{ $readonly ? 'readonly' : '' }} {{ $disabled ? 'disabled' : '' }}
        {{ $format ? "format=$format" : '' }} {{ $formatMin !== null ? "format-min=$formatMin" : '' }}
        {{ $formatMax !== null ? "format-max=$formatMax" : '' }} {!! $attributes !!}
    />
    <button type="button"
            class="px-2 group/new-input-button hidden justify-center items-center new-input--erase-button">
        <svg
            class="icon icon-tabler icon-tabler-backspace w-5 h-5 text-gray-600 dark:text-gray-400 group-hover/new-input-button:text-gray-900 dark:group-hover/new-input-button:text-gray-50"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
            stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M20 6a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-11l-5 -5a1.5 1.5 0 0 1 0 -2l5 -5z"></path>
            <path d="M12 10l4 4m0 -4l-4 4"></path>
        </svg>
    </button>
    <button type="button"
            class="px-2 group/new-input-button hidden justify-center items-center new-input--input-type-button">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="icon icon-tabler icon-tabler-eye w-5 h-5 text-gray-600 dark:text-gray-400 group-hover/new-input-button:text-gray-900 dark:group-hover/new-input-button:text-gray-50"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
             stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
        </svg>
    </button>
</div>
