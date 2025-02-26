@props(['class' => '', 'orders' => [], 'time' => null, 'unseen' => true])

<div class="{{ $class }} flex justify-between gap-2">
    <div class="flex relative flex-row items-center gap-2 text-sm">
        <svg class="icon icon-tabler icon-tabler-clipboard-list h-10 w-10" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
            <path d="M9 12l.01 0"></path>
            <path d="M13 12l2 0"></path>
            <path d="M9 16l.01 0"></path>
            <path d="M13 16l2 0"></path>
        </svg>
        <span>
            @if(!$unseen)
                <div class="absolute top-0 left-0 translate-x-full -translate-y-1/2 text-red-600 font-bold">New</div>
            @endif
            {{ __('admin.No.') }} {{ $orders[0]->id ?? '-'}}
        </span>
    </div>
    <div class="flex items-center gap-2 text-sm">
        <svg class="icon icon-tabler icon-tabler-calendar-time h-10 w-10" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
            <path d="M15 3v4"></path>
            <path d="M7 3v4"></path>
            <path d="M3 11h16"></path>
            <path d="M18 16.496v1.504l1 1"></path>
        </svg>
        <div class="flex flex-col items-end">
            <span>{{ $time ? $time->format('Y-m-d') : '' }}</span>
            <span>{{ $time ? $time->format('H:i') : '' }}</span>
        </div>
    </div>

</div>
