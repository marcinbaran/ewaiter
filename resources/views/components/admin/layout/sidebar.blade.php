<aside id="default-sidebar"
       class="fixed left-0 top-0 z-40 col-start-1 row-span-2 row-start-1 flex h-full w-64 flex-shrink-0 -translate-x-full flex-col font-normal transition-transform duration-75 lg:static lg:flex lg:translate-x-0"
       aria-hidden="true" aria-label="Sidebar">
    <div
        class="relative flex min-h-0 flex-1 flex-col border-r border-gray-300 bg-gray-200 dark:border-gray-700 dark:bg-gray-800 pt-16 lg:pt-0">
        <div class="align-center my-10 flex w-full justify-center">
            <a href="{{ route('admin.dashboard.index') }}" class="w-full flex items-center justify-center">
                <img src="/images/logo.svg" class="block w-1/2" alt="E-waiter logo" />
            </a>
        </div>
        <div class="flex w-full justify-center gap-5">
            <button id="theme-toggle" type="button" data-tooltip-target="tooltip-theme"
                    class="inline-flex cursor-pointer items-center justify-center rounded p-2 text-gray-500 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50">
                <svg id="theme-toggle-dark-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div id="tooltip-theme" role="tooltip"
                 class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                {{ __('admin.tooltips.Theme switcher') }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>

            @can('nav-settings')
                <a href="{{ route('admin.settings.edit', ['settings' => $settingsId])}}"
                   data-tooltip-target="tooltip-settings"
                   class="inline-flex cursor-pointer items-center justify-center rounded p-2 text-gray-500 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                              clip-rule="evenodd"></path>
                    </svg>
                </a>
                <div id="tooltip-settings" role="tooltip"
                     class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                    {{ __('admin.tooltips.Settings') }}
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            @endcan
            <button type="button" data-tooltip-target="tooltip-language" data-dropdown-toggle="language-dropdown"
                    class="inline-flex cursor-pointer items-center justify-center rounded p-2 text-gray-500 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50">
                <img class="mt-0.5 h-5 w-5" src="/images/flags/{{ $currentLanguage }}.png" />
            </button>
            <div id="tooltip-language" role="tooltip"
                 class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                {{ __('admin.tooltips.Language switcher') }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
            <!-- Dropdown -->
            <div
                class="z-50 my-4 hidden list-none divide-y divide-gray-100 rounded bg-gray-600 text-base text-gray-50 shadow dark:bg-gray-700"
                id="language-dropdown">
                <ul role="none">
                    @foreach ($locales as $locale_code)
                        <li>
                            <a href="/admin/localization/{{ $locale_code }}"
                               class="dark:hover:bg-600 block px-4 py-2 text-sm hover:bg-gray-500" role="menuitem">
                                <div class="inline-flex items-center">
                                    <img src="/images/flags/{{ $locale_code }}.png" alt="flags"
                                         class="mr-2 h-3.5 w-3.5">
                                    {{ __('admin.languages.' . $locale_code) }}
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div id="sidebar-navigation" class="relative mb-16 grow overflow-y-auto">
            <ul class="flex-1 space-y-2 px-3" data-accordion="collapse" data-active-classes="block"
                data-inactive-classes="block">
                @foreach ($menu as $id => $conf)
                    @can($conf['can_key'], App\Models\User::class)

                        @if (isset($conf['children']) && !empty($conf['children']))

                            <x-admin.layout.sidebar-expandable-list :item="$conf" :id="$id"
                                                                    :hidden="!$conf['active']"
                                                                    :isExpanded="$conf['active']"
                                                                    :user_roles="$user_roles" />
                        @else
                            <x-admin.layout.sidebar-list-item :route="route($conf['route'])"
                                                              :icon-path="$conf['icon_path']"
                                                              label="{{ __($conf['label_key']) }}"
                                                              :isActive="$conf['active']"
                            />
                        @endif
                    @endcan
                @endforeach
            </ul>
        </div>
        <div
            class="group absolute bottom-0 left-0 z-50 w-full rounded-lg pb-2 pr-2 text-right text-sm text-gray-400 dark:text-gray-600">
            v{{ $version }}
        </div>
    </div>
</aside>
