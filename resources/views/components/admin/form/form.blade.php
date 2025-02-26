@props(['defaultRedirectUrl' => route('admin.dashboard.index'), 'formWide' => 'w-1/2', 'class' => '', 'redirectUrl' => route('admin.dashboard.index') ])
@php
    $currentRoute = request()->getRequestUri();
    $isSettings = str_contains($currentRoute, 'settings');
@endphp
<div class="flex justify-center w-full">
    <form {!! $attributes !!} class="form w-full max-w-3xl lg:{{$formWide}} {{$class}}">
        @csrf
        {{ $slot }}
        <input type="hidden" name="redirect_url" value="{{ $redirectUrl }}">
        <div class="relative w-full my-6 group flex justify-center">
            @if (!$isSettings)
                <x-admin.button type="cancel" color="cancel" href="{{ $redirectUrl }}" class="mr-2">
                    {{ __('admin.Cancel') }}
                </x-admin.button>
            @endif
            <x-admin.button class="flex flex-row justify-between" type="submit" color="success">
                {{ __('admin.Submit') }}
                <span class="button-form"></span>
            </x-admin.button>
        </div>
    </form>
</div>

