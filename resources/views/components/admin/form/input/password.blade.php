@props(['name', 'id', 'class', 'value', 'min', 'max', 'step', 'placeholder', 'required', 'readonly', 'disabled', 'error', 'maxlength','minlength'])

<div class="new-input-parent flex rounded-lg {{ $error ? 'ring-2 ring-red-600' : ''}}">
    <x-admin.form.input.input
        :attributes="$attributes"
        containerClass="flex-grow"
        class="{{ $class }}"
        type="password"
        :min="$min"
        :max="$max"
        :name="$name"
        :id="$id"
        :value="$value"
        :placeholder="$placeholder"
        :required="$required"
        :readonly="$readonly"
        :disabled="$disabled"
        :maxlength="$maxlength"
        :minlength="$minlength"
    />
</div>
