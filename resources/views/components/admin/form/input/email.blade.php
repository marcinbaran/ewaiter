@props(['name', 'id', 'class', 'value', 'min', 'max', 'placeholder', 'required', 'readonly', 'disabled', 'error', 'showIcon'])

<div class="new-input-parent flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    @if($showIcon)
        <div
            class="select-none inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
            <svg class="icon icon-tabler icon-tabler-at h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                <path d="M16 12v1.5a2.5 2.5 0 0 0 5 0v-1.5a9 9 0 1 0 -5.5 8.28"></path>
            </svg>
        </div>
    @endif

    <x-admin.form.input.input
        :attributes="$attributes"
        containerClass="flex-grow {{ $showIcon ? 'rounded-l-none border-l-0' : ''}}"
        class="{{ $class }}"
        type="email"
        :name="$name"
        :id="$id"
        :value="$value"
        :minlength="$min"
        :maxlength="$max"
        :placeholder="$placeholder"
        :required="$required"
        :readonly="$readonly"
        :disabled="$disabled"
    />
</div>
