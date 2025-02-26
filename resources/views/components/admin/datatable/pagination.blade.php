<div class="mt-4 px-4 md:p-0 flex flex-col md:flex-row sm:flex justify-between items-center space-y-4 sm:space-y-0">

    @if($numberOfPages > 1)
    <div>
        <p class="paged-pagination-results text-sm text-gray-700 leading-5 dark:text-white">
            <span>
                {{__('admin.Show')}}
            </span>
            <span class="font-medium">{{$fromItems}}</span>
            <span>
                {{__('admin.To')}}
            </span>
            <span class="font-medium">{{$toItems}}</span>
            <span>
                {{__('admin.Of')}}
            </span>
            <span class="font-medium">{{$numberOfItems}}</span>
            <span>
                {{__('admin.Results')}}
            </span>
        </p>
    </div>
    @elseif($numberOfPages == 1)
    <div>
        <p class="paged-pagination-results text-sm text-gray-700 leading-5 dark:text-white">
            <span>
                {{__('admin.Show')}}
            </span>
            <span class="font-medium">{{$numberOfItems}}</span>
            <span>
                {{__('admin.Results')}}
            </span>
        </p>
    </div>
    @endif
    <div class="w-full md:w-auto">
        @if($numberOfPages > 0)
        <nav class="flex items-center justify-between" aria-label="Pagiantion Navigation">
            <div class="hidden md:flex-1 md:flex md:items-center md:justify-between">
                @if($page > 1)
                <button wire:click='previousPage' class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                     </svg>
                </button>
                @else
                <div class="cursor-not-allowed relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                     </svg>
                </div>
                @endif
                @foreach ($displayingPages as $number)
                    <button wire:click='setPage({{$number}})' class="relative @if($number == $page) bg-gray-100 dark:bg-gray-500 @else bg-white dark:bg-gray-700  @endif  inline-flex items-center px-4 py-2 -ml-px text-sm font-medium   border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150  dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                        {{ $number }}
                    </button>
                @endforeach

                @if($page < $numberOfPages)
                <button wire:click='nextPage' class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                     </svg>
                </button>
                @else
                <div class="cursor-not-allowed relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                     </svg>
                </div>
                @endif
            </div>
            <div class="md:hidden w-full flex items-center justify-around">
                @if($page > 1)
                <button wire:click='previousPage' class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                     </svg>
                     {{__('admin.Previous')}}
                </button>
                @else
                <div class=" cursor-not-allowed relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                     </svg>
                     {{__('admin.Previous')}}
                </div>
                @endif

                @if($page < $numberOfPages)
                <button wire:click='nextPage' class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    {{__('admin.Next')}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                     </svg>
                     

                </button>
                @else
                <div class="cursor-not-allowed relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:border-gray-600 dark:hover:bg-gray-600">
                    {{__('admin.Next')}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                     </svg>
                    
                </div>
                @endif
            </div>
        </nav>
        @endif
    </div>
</div>