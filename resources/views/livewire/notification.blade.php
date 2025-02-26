<div>
    <button class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50" data-tooltip-target="tooltip-notifications-button" data-dropdown-toggle="notification-dropdown" type="button">
        <span class="sr-only">{{ __('admin.View Notifications') }}</span>
        <div class="notification-badge">

            @if ($notificationsCount > 0)
            <span class="absolute -right-1 -top-1 flex h-[1.25rem] w-[1.25rem] items-center justify-center rounded-full bg-red-600 text-center text-xs font-bold text-zinc-100">
                {{ $notificationsCount }}
            </span>
            @endif
        </div>

        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
            </path>
        </svg>
    </button>

    <div class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700" id="tooltip-notifications-button" role="tooltip">
        {{ __('admin.tooltips.Notifications') }}
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>


    <div class="z-50 hidden h-72 w-full overflow-auto rounded-lg border border-gray-300 bg-gray-200 p-2 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 md:w-96" id="notification-dropdown" wire:ignore.self>
        @if(auth()->check())
        <a class="notifications-reload block h-0 w-0 bg-transparent" wire:click="reloadNotificationComponent"></a>

        <div class="flex w-full items-center justify-between p-2">
            <span class="text-primary-900 dark:text-primary-700 font-bold">{{ __('admin.Notifications') }}</span>
            @if ($notificationsCount > 0)
                <a class="flex items-center justify-center gap-1 rounded-lg p-2 text-sm hover:bg-gray-600 hover:text-gray-50 dark:hover:bg-gray-700 dark:hover:text-gray-50" href="#" wire:click="markAllAsRead">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    {{ __('admin.Read All') }}
                </a>
            @endif
        </div>

        <div class="h-px w-full self-center bg-gray-300 dark:bg-gray-700"></div>


        <div class="notification-body flex w-full h-max flex-col gap-2 divide-y divide-gray-300 overflow-y-hidden pt-2 dark:divide-gray-700">
            @forelse($notifications as $notification)
            @php
            $notification['url'] = str_replace('orders', 'bills', $notification['url']);
            @endphp
            <div class="@if (!$notification['read_at']) h-fit font-bold @endif flex w-full flex-row items-center justify-between gap-2 p-2 hover:rounded-lg hover:bg-gray-600 hover:text-gray-50 dark:hover:bg-gray-700 dark:hover:text-gray-50">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
                <a class="flex grow flex-col overflow-x-hidden text-sm" href="{{ $notification['url'] }}" wire:click="markAsRead('{{ $notification['id'] }}')">
                    <span class="text-md capitalize">
                        {{ $notification['title'] = str_replace('_', ' ', $notification['title']) }}
                    </span>
                    <span>
                        {{ $notification['content'] }}
                    </span>
                    <span class="mt-1 text-xs">
                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                    </span>
                </a>
                @if (!$notification['read_at'])
                <a class="read-notification hover:text-primary-500 dark:hover:text-primary-700 flex items-center justify-center p-2" href="#" wire:click="markAsRead('{{ $notification['id'] }}')">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
                @endif
            </div>
            @empty
            <p class="mt-4 text-center">{{ __('admin.No Notification') }}</p>
            @endforelse
        </div>

        <div wire:loading class="flex mx-auto flex-col w-full h-auto items-center justify-center py-4"  >
            <x-admin.layout.loader class="mx-auto w-full flex justify-center items-center"/>
        </div>
  


        @else
        <x-admin.logged-out class="w-full h-full" />
        @endif
    </div>

</div>