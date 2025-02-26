@spaceless<div class="flex items-start">
    @if ($photo)
        <div class="{{ $class??'self-start mr-3' }}">
            <img src="/{{ $photo }}" alt="{{ isset($name)? __($name) : '' }}" class="w-[90px]" width="{{ $width??'auto' }}" height="{{ $height??'auto' }}" />
        </div>
    @endif
    <div class="flex-1">
        <h5 class="mt-0">{{ isset($name)? __($name) : '' }}</h5>
        <p class="italic font-normal text-white lowercase">{!! isset($description)? __($description) : '' !!}</p>
    </div>
</div>@endspaceless
