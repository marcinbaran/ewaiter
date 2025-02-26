@props(['class' => ''])

<div class="orders-zero-data {{ $class }} flex w-full h-full cursor-pointer flex-col items-center justify-center gap-6 rounded-lg"
    wire:click="refresh">

    <svg class="icon icon-tabler icon-tabler-search h-16 w-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
        <path d="M21 21l-6 -6"></path>
    </svg>
    <p class="text-lg">{{ __('admin.Here will be new orders') }}</p>

    <div class="flex gap-2"><svg class="icon icon-tabler icon-tabler-reload h-6 w-6" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M19.933 13.041a8 8 0 1 1 -9.925 -8.788c3.899 -1 7.935 1.007 9.425 4.747"></path>
            <path d="M20 4v5h-5"></path>
        </svg>{{ __('admin.Click to refresh') }}
    </div>
    <div class="h-16 flex items-center justify-center">
        <div wire:loading>
            <x-admin.layout.loader />
        </div>
    </div>
</div>
