@php
    $values = explode('-', $value);
@endphp

<div class="flex space-between gap-6 time-range">
    <input type="hidden" name="value[{{ $key }}]" value="{{ $value }}" id="time-range-{{ $loop->index }}-hidden" />
    <x-admin.form.new-input containerClass="min-w-0 flex-1" type="time" id="time-range-{{ $loop->index }}-from" :value="$values[0]" />
    <x-admin.form.new-input containerClass="min-w-0 flex-1" type="time" id="time-range-{{ $loop->index }}-to" :value="$values[1]"/>
</div>
