<x-admin.button type="link" color="success" href="#" id="newAdditionButton" class="text-white flex cursor-pointer text-center">
    {{ __('admin.Create') }}
</x-admin.button>

<div id="newAdditionModal" wire:ignore.self tabindex="-1" aria-hidden="true"
    class="fixed top-0 left-0 z-60 hidden w-screen h-screen p-6">
    <div class="newAdditionModalClose absolute inset-0"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col gap-3 rounded-lg text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-800 p-3">
        <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-700 text-xl font-bold pb-3">
            <h3>{{ __('admin.Create new addition') }}</h3>
            <button type="button" class="newAdditionModalClose w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">{{ __('admin.Close') }}</span>
            </button>
        </div>
            <div>
                <x-admin.form.label value="{{ __('admin.Name') }}" required="true" for="addition_name" />
                <x-admin.form.new-input wire:model="additionName" max="50" id="addition_name" type="text" />
                @error('addition_name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <x-admin.form.label value="{{ __('admin.Price') }}" required="true" for="addition_price" />
                <x-admin.form.new-input wire:model="additionPrice" id="addition_price" type="money" />
                @error('addition_name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        <div class="flex justify-center gap-2 border-t border-gray-300 dark:border-gray-700 pt-3">
            <x-admin.button type="link" color="success" wire:click="createAddition" class="newAdditionModalClose">
                {{ __('admin.Create') }}
            </x-admin.button>
            <x-admin.button type="link" color="danger" class="newAdditionModalClose">
                {{ __('admin.Cancel') }}
            </x-admin.button>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.Create_will_automatically_assign_addition_to_group') }}</div>
    </div>
</div>
