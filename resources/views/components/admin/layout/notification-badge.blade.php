@if ($notificationsCount > 0)
    <span
        class="absolute -right-1 -top-1 flex h-[1.25rem] w-[1.25rem] items-center justify-center rounded-full bg-red-600 text-center text-xs font-bold text-zinc-100">
        {{ $notificationsCount }}
    </span>
@endif
