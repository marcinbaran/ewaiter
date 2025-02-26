@props(['name', 'id', 'class', 'value', 'placeholder', 'required', 'readonly', 'disabled', 'error', 'showIcon'])

<div class="new-input-parent flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    @if($showIcon)
        <div
            class="select-none inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 icon icon-tabler icon-tabler-building-bank" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M3 21l18 0"></path>
                <path d="M3 10l18 0"></path>
                <path d="M5 6l7 -3l7 3"></path>
                <path d="M4 10l0 11"></path>
                <path d="M20 10l0 11"></path>
                <path d="M8 14l0 3"></path>
                <path d="M12 14l0 3"></path>
                <path d="M16 14l0 3"></path>
            </svg>
        </div>
    @endif

    <x-admin.form.input.input
        :attributes="$attributes"
        containerClass="flex-grow {{ $showIcon ? 'rounded-l-none border-l-0' : ''}}"
        class="input-mask {{ $class }}"
        type="text"
        format="bank-account"
        :name="$name"
        :id="$id"
        :value="$value"
        :placeholder="$placeholder"
        :required="$required"
        :readonly="$readonly"
        :disabled="$disabled"
    />
</div>
