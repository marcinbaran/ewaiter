@props(['isInCart' => false])
<div class="grid grid-cols-3 my-auto scale-[0.80] lg:scale-100 {{($isInCart) ? '' : 'gap-1'}} gap-1 w-full">
    <svg wire:click="decreaseQuantity" class="cursor-pointer m-auto" width="32" height="32" viewBox="0 0 24 24"
         fill="none"
         xmlns="http://www.w3.org/2000/svg">
        <rect x="23.5" y="23.5" width="23" height="23" rx="11.5" transform="rotate(180 23.5 23.5)"
              stroke="#E5E7EB" />
        <path d="M7 12H17" stroke="#596273" stroke-width="2" stroke-linecap="round" />
    </svg>
    <div class="{{ $isBackground ? 'bg-[#F3F4F6]' : ''}} p-2 text-center">
        <span>{{ $quantity }}</span>
    </div>
    <svg wire:click="increaseQuantity" class="cursor-pointer m-auto" width="32" height="32" viewBox="0 0 24 24"
         fill="none"
         xmlns="http://www.w3.org/2000/svg">
        <rect x="23.5" y="23.5" width="23" height="23" rx="11.5" transform="rotate(180 23.5 23.5)"
              stroke="#E5E7EB" />
        <path d="M7 12H17" stroke="#596273" stroke-width="2" stroke-linecap="round" />
        <path d="M12 7L12 17" stroke="#596273" stroke-width="2" stroke-linecap="round" />
    </svg>
</div>
