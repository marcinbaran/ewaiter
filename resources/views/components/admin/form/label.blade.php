@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'mb-1 block text-sm font-medium text-gray-600 dark:text-gray-400']) }}>
    {!! $value ?? $slot !!}
    @if(!$required)
        <span class="font-light">({{ __('admin.Optional') }})</span>
    @endif
</label>
