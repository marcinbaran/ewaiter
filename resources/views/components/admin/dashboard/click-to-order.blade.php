<div class="absolute top-0 left-0 w-full h-full flex justify-center items-center rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50" {!! $attributes !!} >
    <div class="flex flex-col items-center justify-center gap-4 text-gray-900 dark:text-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt w-32 h-auto" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2"></path>
        </svg>
        <x-admin.button color="cancel" x-on:click.away="clickToOrder = false"  @click="clickToOrder = false">{{ __('admin.Start receving orders') }}</x-admin.button>
    </div>
</div>
