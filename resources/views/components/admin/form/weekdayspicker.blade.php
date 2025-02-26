@props(['data', 'name' => '', 'id' => ''])

<div class="weekdayspicker">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-grow flex gap-3 justify-between">
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Monday') }}" name="{{ $name . '[m]' }}" id="{{ $id . '_m' }}" :checked="old('availability.m', !isset($data->availability) || $data->availability->m)" />
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Tuesday') }}" name="{{ $name . '[t]' }}" id="{{ $id . '_t' }}" :checked="old('availability.t', !isset($data->availability) || $data->availability->t)" />
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Wednesday') }}" name="{{ $name . '[w]' }}" id="{{ $id . '_w' }}" :checked="old('availability.w', !isset($data->availability) || $data->availability->w)" />
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Thursday') }}" name="{{ $name . '[r]' }}" id="{{ $id . '_r' }}" :checked="old('availability.r', !isset($data->availability) || $data->availability->r)" />
        </div>
        <div class="flex-grow flex gap-3 justify-between">
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Friday') }}" name="{{ $name . '[f]' }}" id="{{ $id . '_f' }}" :checked="old('availability.f', !isset($data->availability) || $data->availability->f)" />
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Saturday') }}" name="{{ $name . '[s]' }}" id="{{ $id . '_s' }}" :checked="old('availability.s', !isset($data->availability) || $data->availability->s)" />
            <x-admin.form.daypicker class="flex-1" value="{{ __('admin.weekdays.Sunday') }}" name="{{ $name . '[u]' }}" id="{{ $id . '_u' }}" :checked="old('availability.u', !isset($data->availability) || $data->availability->u)" />
        </div>
    </div>
</div>
