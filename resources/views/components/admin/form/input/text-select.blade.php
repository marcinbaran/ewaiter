@props(['name', 'id', 'class', 'value', 'min', 'max', 'placeholder', 'required', 'readonly', 'disabled', 'error'])

<div class="new-input-parent inline-flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    <x-admin.form.input.input
        :attributes="$attributes"
        containerClass="flex-grow rounded-r-none"
        class="{{ $class }}"
        type="text"
        :minlength="$min"
        :maxlength="$max"
        name="{{$name}}-value"
        :id="$id"
        :value="$value"
        :placeholder="$placeholder"
        :required="$required"
        :readonly="$readonly"
        :disabled="$disabled"
    />
    <select
        class="select-none inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-50 focus:ring-0 focus:border-transparent disabled:text-gray-600 disabled:dark:text-gray-400 opacity-100 disabled:cursor-not-allowed"
        name="{{$name}}-select" {{($disabled || $readonly) ? 'disabled' : ''}}>
        @if ($slot->isEmpty())
            <option>Empty</option>
        @else
            {{ $slot }}
        @endif
    </select>
</div>
