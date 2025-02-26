<nav
    class="col-span-2 z-50 col-start-1 row-start-1 border-b border-gray-300 bg-gray-200 dark:border-gray-700 dark:bg-gray-800 lg:col-auto lg:col-start-2">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start gap-2">
                {{-- data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" --}}
                <button aria-controls="default-sidebar" type="button" id="toggleSidebarMobile" aria-expanded="true"
                        class="cursor-pointer rounded-lg p-2 text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50 lg:hidden">
                    <svg id="toggleSidebarMobileHamburger" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                              clip-rule="evenodd"></path>
                    </svg>
                    <svg id="toggleSidebarMobileClose" class="hidden h-6 w-6" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"></path>
                    </svg>
                </button>
                <x-admin.layout.quick-actions />
                <div
                    class="dark:hover:ring-primary-700 hidden h-10 w-48 items-center justify-start rounded-lg bg-gray-300 p-2 text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:hover:ring-2 md:flex">
                    <button id="showSearch" class="showSearch flex h-full w-full items-center justify-start">
                        <span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="ml-2">{{ __('admin.Search') }}</span>
                    </button>
                </div>
                <livewire:search-bar-modal />
            </div>
            <div class="flex items-center">
                <x-admin.layout.push-notification-request />
                <button id="showSearch"
                        class="showSearch flex aspect-square h-10 items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50 md:hidden"
                        data-tooltip-target="tooltip-search-button">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                              clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div id="tooltip-search-button" role="tooltip"
                     class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                    {{ __('admin.tooltips.Search dialog') }}
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                @if($isWebsite === 1)
                    <div id="notifications">
                        <notification-wrapper />
                    </div>
                @endif
                <!-- Profile -->
                <div class="ml-3 flex items-center">
                    <button type="button"
                            class="flex rounded-full text-sm text-gray-600 bg-gray-300 dark:text-gray-400 dark:bg-gray-700 hover:text-gray-50 hover:bg-gray-600 dark:hover:text-gray-50 dark:hover:ring-2 dark:hover:ring-primary-700"
                            id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2"
                            data-tooltip-target="tooltip-profile">
                        <span class="sr-only">Open user menu</span>
                        <div class="w-8 aspect-square flex justify-center items-center">
                            {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                        </div>
                    </button>
                    <div id="tooltip-profile" role="tooltip"
                         class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                        {{ __('admin.tooltips.Profile') }}
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                    <!-- Dropdown menu -->
                    <div
                        class="z-50 hidden divide-y divide-gray-300 rounded-lg border border-gray-300 bg-gray-200 p-2 text-gray-600 dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                        id="dropdown-2">
                        <div class="flex flex-col p-2 text-sm" role="none">
                            <p class="font-bold" role="none">
                                {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                            </p>
                            <p class="truncate font-medium" role="none">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                        <ul class="flex list-none flex-col gap-2 pt-2" role="none">
                            <li>
                                <a href="{{ route('admin.dashboard.index') }}"
                                   class="block rounded-lg p-2 text-sm text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50"
                                   role="menuitem">
                                    {{ __('admin.Dashboard') }}
                                </a>
                            </li>
                            @can('nav-settings')
                                <li>
                                    <a href="{{ route('admin.settings.index') }}"
                                       class="block rounded-lg p-2 text-sm text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50"
                                       role="menuitem">
                                        {{ __('admin.Settings') }}
                                    </a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('admin.auth.logout') }}"
                                   class="block rounded-lg p-2 text-sm text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50"
                                   role="menuitem">
                                    {{ __('admin.Logout') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
