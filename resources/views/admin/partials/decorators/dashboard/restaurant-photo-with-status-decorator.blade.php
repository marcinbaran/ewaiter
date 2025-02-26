@props(['photoUrl' => '', 'photoAlt' => '', 'restaurantName' => '', 'restaurantIsActive' => false])

<div class="flex items-center gap-4">
    <div class="relative">
        <img src="{{ $photoUrl }}" alt="{{ $photoAlt  }}" class="w-10 h-10 rounded-full shadow-lg">
        <div
            class="rounded-full w-3 h-3 absolute top-0 right-0 -translate-y-1 translate-x-1 {{ $restaurantIsActive ? 'bg-green-500' : 'bg-red-500'}}"></div>
    </div>
    <span>{{$restaurantName}}</span>
</div>

