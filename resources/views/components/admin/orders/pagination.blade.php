@props(['title' => '', 'page' => 1, 'numberOfOrders' => 1, 'class' => '', 'hasUnseen' => false, 'numberOfUnseenOrders' => 0])
<div
    class=" new-order--pagination {{ $class }} flex w-full flex-row items-center justify-between border-b border-gray-300 pb-2 dark:border-gray-700">
    <h2 class="text-lg flex flex-row items-center gap-2 font-bold">
        {!! $title !!}
        @if($hasUnseen && $numberOfUnseenOrders > 0)
            <div class="new-order-badge animate-pulse  flex items-center justify-center text-sm text-gray-50 w-5 h-5 rounded-full bg-red-700"></div>    
        @endif
        
    </h2>
    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
        @if ($page > 1)
            <svg class="icon icon-tabler icon-tabler-chevrons-left h-5 w-5 cursor-pointer hover:text-gray-900 dark:hover:text-gray-50"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round" wire:click="first">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M11 7l-5 5l5 5"></path>
                <path d="M17 7l-5 5l5 5"></path>
            </svg>
            <svg class="icon icon-tabler icon-tabler-chevron-left h-5 w-5 cursor-pointer hover:text-gray-900 dark:hover:text-gray-50"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                fill="none" stroke-linecap="round" stroke-linejoin="round" wire:click="previous">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M15 6l-6 6l6 6"></path>
            </svg>
        @endif
        <span class="w-16 text-center select-none font-bold text-gray-900 dark:text-gray-50">{{ $page }}/{{ $numberOfOrders }}</span>
        @if ($page < $numberOfOrders && $numberOfOrders > 1)
            <svg class="icon icon-tabler icon-tabler-chevron-right h-5 w-5 cursor-pointer hover:text-gray-900 dark:hover:text-gray-50"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                fill="none" stroke-linecap="round" stroke-linejoin="round" wire:click="next">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 6l6 6l-6 6"></path>
            </svg>
            <svg class="icon icon-tabler icon-tabler-chevrons-right h-5 w-5 cursor-pointer hover:text-gray-900 dark:hover:text-gray-50"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                fill="none" stroke-linecap="round" stroke-linejoin="round" wire:click="last">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M7 7l5 5l-5 5"></path>
                <path d="M13 7l5 5l-5 5"></path>
            </svg>
        @endif
    </div>
</div>
