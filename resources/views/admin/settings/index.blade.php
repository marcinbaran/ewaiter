<x-admin.layout.admin-layout>
    <div
        class="settings-container relative flex justify-center gap-8 md:justify-start">
        <div class="w-full md:w-[65%]">
            <x-admin.settings.settings-form :data="$data" :settings="$settings" :tpayDisabled="$tpayDisabled" />
        </div>
        <div
            class="settings-menu absolute right-1/2 z-30 w-full -translate-y-full -translate-x-full rounded-lg bg-gray-200 dark:bg-gray-800 py-2 border border-gray-300 dark:border-gray-700 md:relative md:right-0 md:top-0 md:w-1/4 md:translate-y-0 md:translate-x-0 md:border-none md:bg-transparent md:dark:bg-transparent">
            <div
                class="flex flex-col w-full gap-4 p-4 sticky top-8 max-h-64 overflow-y-auto lg:max-h-none lg:overflow-visible"
                id="settings-menu">

                @foreach ($settings as $setting)
                    @if ($setting['key'] != 'przelewy24')
                        <x-admin.settings.setting
                            :route="route('admin.settings.edit', ['settings' => $setting['id']])"
                            :key="$setting['key']" :name="$setting['name'] ?? $setting['key']" />
                    @endif
                @endforeach

            </div>
        </div>
    </div>
</x-admin.layout.admin-layout>
