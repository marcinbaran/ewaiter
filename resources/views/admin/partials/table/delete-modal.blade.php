<div class="before-tools">
    @if ($showDeleteModal)
        <div data-ps-reload="1" class="fixed inset-0 z-50">
            <div wire:click="closeDeleteModal()" class="absolute inset-0 bg-black bg-opacity-25"></div>
            <div
                class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 flex-col justify-center gap-4 rounded-lg border border-gray-300 bg-gray-200 p-4 text-gray-600 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                <h3 class="text-lg font-normal">
                    {{ __('admin.Are you sure you want to delete the data?') }}</h3>
                <div class="buttons flex justify-center gap-2">
                    <x-admin.button color="cancel"
                        wire:click="closeDeleteModal()">{{ __('admin.No, cancel') }}</x-admin.button>
                    <x-admin.button color="danger"
                        wire:click="confirmDelete()">{{ __('admin.Yes, delete') }}</x-admin.button>
                </div>
            </div>
        </div>
    @endif
</div>
