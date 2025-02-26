@props(['name', 'id', 'class', 'value', 'min', 'max', 'placeholder', 'required', 'readonly', 'disabled', 'error', 'showIcon'])

<div class="new-input-parent flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    @if($showIcon)
        <div
            class="select-none inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
            <svg class="icon icon-tabler icon-tabler-clock h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                <path d="M12 7v5l3 3"></path>
            </svg>
        </div>
    @endif

    <x-admin.form.input.input
        containerClass="flex-grow {{ $showIcon ? 'rounded-l-none border-l-0' : ''}}" class="flatpickr-timepicker {{ $class }}"
        type="text" :attributes="$attributes" :name="$name" :id="$id" :value="$value" :min="$min"
        :max="$max" :placeholder="$placeholder" :required="$required" :readonly="$readonly" :disabled="$disabled" />
</div>
