<div class="relative max-md:absolute max-md:bottom-5 max-md:right-5 max-md:z-50"
     x-data="{ open: false, childElementOpen: false }" x-on:keydown.escape.stop="if (!childElementOpen){ open = false}"
     x-on:mousedown.away="if (!childElementOpen) {open = false}">
    <div>
        <button
            class="dark:text-primary-700 bg-primary-900 dark:hover:ring-primary-700 group flex h-10 w-10 items-center justify-center rounded-lg text-gray-50 hover:bg-gray-600 dark:bg-gray-700 dark:hover:ring-2 max-md:rounded-full"
            data-tooltip-target="tooltip-quick-actions" aria-haspopup="true" aria-expanded="false"
            x-bind:aria-expanded="open" x-on:click="open = !open">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
            </svg>
        </button>
        <div
            class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700"
            id="tooltip-quick-actions" role="tooltip">
            {{ __('admin.tooltips.Quick actions') }}
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>
        <div
            class="absolute z-40 mt-2 flex w-96 flex-col rounded-lg border border-gray-300 bg-gray-200 p-2 dark:border-gray-700 dark:bg-gray-800 max-md:mt-0 max-md:w-72"
            role="menu" aria-orientation="vertical" aria-labelledby="filters-menu" x-show="open"
            x-on:click.away="open = false" x-bind:class="{
                'opacity-100 transform -translate-y-full -translate-x-[calc(100%_+_0.5rem)]': open && window
                    .innerWidth < 768,
                'opacity-100': open && window.innerWidth >= 768
            }" x-transition:enter="opacity-0" x-transition:enter-start="opacity-0" x-transicton:enter-end="opacity-100"
            x-transition:leave="opacity-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            x-cloak>
            <span class="text-primary-900 dark:text-primary-700 p-2 font-bold">{{ __('admin.Quick Actions') }}</span>
            <div class="h-px w-full self-center bg-gray-300 dark:bg-gray-700"></div>
            <ul class="flex flex-col gap-2 pt-2">
                @foreach (config('options_visibility.quickActions') as $quickAction)
                    @if (!empty(array_intersect($quickAction['roles'], $user_roles)))
                        @if ($quickAction['isWebsite'] == $isWebsite)
                            <x-admin.layout.quick-actions-item :route="$quickAction['link']"
                                                               :icon="$quickAction['icon']"
                                                               :text="$quickAction['text']" />
                        @endif
                    @endif
                @endforeach

            </ul>
        </div>
    </div>
</div>
