@props(['name', 'id', 'class', 'value', 'min', 'max', 'placeholder', 'required', 'readonly', 'disabled', 'error', 'prefix' => '', 'suffix' => ''])

<div class="new-input-parent inline-flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    @if($prefix !== '')
        <div
            class="select-none inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
            {{ $prefix }}
        </div>
    @endif
    <x-admin.form.input.input
        :attributes="$attributes"
        containerClass="flex-grow {{ $prefix !== '' ? 'rounded-l-none' : '' }} {{ $suffix !== '' ? 'rounded-r-none' : '' }}"
        :class="$class"
        type="text"
        :minlength="$min"
        :maxlength="$max"
        :name="$name"
        :id="$id"
        :value="$value"
        :placeholder="$placeholder"
        :required="$required"
        :readonly="$readonly"
        :disabled="$disabled"
    />
    @if($suffix !== '')
        <div
            class="select-none inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
            {{ $suffix }}
        </div>
    @endif
</div>
