<div class="dashboard-notifications w-full h-[{{$perPage*6}}rem] flex gap-6 overflow-hidden {{$class}}" wire:poll.15s>
    <input type="hidden" class="tailwind-classes-loader translate-y-24 -translate-y-24 transition-transform">
    @if($notificationsCount > 0)
        <ul class="dashboard-notifications--list relative flex-1">
            <li class="dashboard-notification--before w-full h-24 -translate-y-full absolute top-0 left-0 py-3 px-4 flex gap-3 rounded-lg text-gray-600 dark:text-gray-400 cursor-pointer group/notification hover:text-gray-900 dark:hover:text-gray-50">
                <div class="notification--icon flex justify-center items-center mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 icon icon-tabler icon-tabler-message" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 9h8" /><path d="M8 13h6" /><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" /></svg>
                </div>
                <div class="notification--content flex-1">
                    <p class="notification--title">{{ $notificationBefore['title'] }}</p>
                    <p class="notification--description text-md">{{ $notificationBefore['description'] }}</p>
                    <p class="notification--time text-xs mt-1">{{ $notificationBefore['created_at'] }}</p>
                </div>
                <div class="notification--button flex justify-center items-center group-hover/notification:text-primary-900 dark:group-hover/notification:text-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-check" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                </div>
            </li>
            @foreach($notifications as $notification)
                <li class="dashboard-notification py-3 px-4 flex gap-3 rounded-lg text-gray-600 dark:text-gray-400 h-24 cursor-pointer group/notification hover:text-gray-900 dark:hover:text-gray-50" wire:click="markAsComplete('{{$notification['id']}}')">
                    <div class="notification--icon flex justify-center items-center mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 icon icon-tabler icon-tabler-message" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 9h8" /><path d="M8 13h6" /><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" /></svg>
                    </div>
                    <div class="notification--content flex-1">
                        <p class="notification--title">{{ $notification['title'] }}</p>
                        <p class="notification--description text-md">{{ $notification['description'] }}</p>
                        <p class="notification--time text-xs mt-1">{{ $notification['created_at'] }}</p>
                    </div>
                    <div class="notification--button flex justify-center items-center group-hover/notification:text-primary-900 dark:group-hover/notification:text-primary-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-check" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </div>
                </li>
            @endforeach
            <li class="dashboard-notification--after w-full h-24 absolute bottom-0 left-0 translate-y-full py-3 px-4 flex gap-3 rounded-lg text-gray-600 dark:text-gray-400 cursor-pointer group/notification hover:text-gray-900 dark:hover:text-gray-50">
                <div class="notification--icon flex justify-center items-center mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 icon icon-tabler icon-tabler-message" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 9h8" /><path d="M8 13h6" /><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" /></svg>
                </div>
                <div class="notification--content flex-1">
                    <p class="notification--title">{{ $notificationAfter['title'] }}</p>
                    <p class="notification--description text-md">{{ $notificationAfter['description'] }}</p>
                    <p class="notification--time text-xs mt-1">{{ $notificationAfter['created_at'] }}</p>
                </div>
                <div class="notification--button flex justify-center items-center group-hover/notification:text-primary-900 dark:group-hover/notification:text-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-check" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                </div>
            </li>
        </ul>
        <div class="dashboard-notifications--buttons p-3 flex flex-col gap-6">
            <button class="scroll-up flex-1 text-gray-900 dark:text-gray-50 bg-gray-300 dark:bg-gray-600 rounded-lg px-2 hover:text-primary-900 dark:hover:text-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-arrow-narrow-up" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M16 9l-4 -4" /><path d="M8 9l4 -4" /></svg>
            </button>
            <button class="scroll-down flex-1 text-gray-900 dark:text-gray-50 bg-gray-300 dark:bg-gray-600 rounded-lg px-2 hover:text-primary-900 dark:hover:text-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-arrow-narrow-down" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M16 15l-4 4" /><path d="M8 15l4 4" /></svg>
            </button>
        </div>
    @else
        <x-admin.orders.zero-data />
    @endif
</div>
