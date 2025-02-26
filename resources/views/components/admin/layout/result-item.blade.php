<a href="{{ $item['url'] }}" class="group grow flex w-full cursor-pointer flex-row items-center justify-between rounded-lg p-2 hover:text-gray-50 hover:bg-gray-600 dark:hover:text-gray-50 dark:hover:bg-gray-700">
    <div class="flex flex-row items-center justify-start">
        @if ($item['photo'] != '')
            <img class="h-auto w-24 rounded-lg" src="{{ $item['photo'] }}" />
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
            </svg>
        @endif
        <div class="ml-2 flex flex-col">
            <label class="font-semibold text-gray-900 dark:text-gray-400 group-hover:text-gray-50">{{ $item['title'] }}</label>
            <p class="text-sm italic">{{ $item['description'] }}</p>
        </div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="mr-2 hidden h-5 w-5 group-hover:block text-primary-500 dark:text-primary-700">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
    </svg>
</a>
