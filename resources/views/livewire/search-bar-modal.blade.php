<dialog id="searchModal" wire:ignore.self class="w-full h-full bg-transparent">
    <div class="relative overflow-hidden w-full h-full mx-auto md:w-2/3 p-2 rounded-lg border bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border-gray-300 dark:border-gray-700">
        <form action="#" method="GET" class="flex w-full items-center justify-center gap-2 py-4">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
            </svg>
            <input type="text" wire:model="phrase" id="topbar-search" class="grow border-none bg-transparent p-0 focus:outline-none focus:ring-0" placeholder="{{__('admin.Search')}}">
        </form>
        <div class="h-px self-center bg-gray-300 dark:bg-gray-700"></div>
        <div class="flex flex-col divide-y divide-gray-300 dark:divide-gray-700">
            @if(auth()->check())
            <div wire:loading.flex class="w-full h-full flex items-center justify-center py-4">
                <x-admin.layout.loader />
            </div>
         
            @forelse($data as $result)
            <x-admin.layout.search-item label="{{ $result['label'] }}" :items="$result['items']" />
            @empty
            <div class="flex h-full w-full items-center justify-center py-4">
                <p class="text-gray-400 dark:text-gray-600">{{ __('admin.No results found') }}</p>
            </div>
            @endforelse
            @else
            <x-admin.logged-out class="w-full h-full" />
            @endif

        </div>
    </div>
</dialog>