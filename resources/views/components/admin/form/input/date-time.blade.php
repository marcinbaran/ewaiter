@props(['name', 'id', 'class', 'value', 'min', 'max', 'minTime','placeholder', 'required', 'readonly', 'disabled', 'error', 'showIcon','step'])

<div class="new-input-parent flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    @if($showIcon)
        <div
            class="select-none inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-300 px-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-time w-5 h-5"
                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                 stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                <path d="M15 3v4"></path>
                <path d="M7 3v4"></path>
                <path d="M3 11h16"></path>
                <path d="M18 16.496v1.504l1 1"></path>
            </svg>
        </div>
    @endif

    <x-admin.form.input.input
        containerClass="flex-grow {{ $showIcon ? 'rounded-l-none border-l-0' : ''}}"
        class="flatpickr-datetimepicker {{ $class }}"
        type="text" :attributes="$attributes" :name="$name" :id="$id" :value="$value" :min="$min"
        :max="$max" :minTime="$minTime" :placeholder="$placeholder" :required="$required" :readonly="$readonly"
        :disabled="$disabled" :step="$step" />
</div>
