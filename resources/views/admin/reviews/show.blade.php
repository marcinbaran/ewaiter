<x-admin.layout.admin-layout>
    <div class="grid grid-cols-12 gap-4">
        <x-admin.show.details-block class="col-span-12 md:col-span-6 xl:col-span-9" :title="__('admin.review')"
                                    id="bills_ordered_items">
            @include('admin.reviews.partials.review_details')
        </x-admin.show.details-block>
        <x-admin.show.details-block :title="__('admin.Client details')" id="bills_client_details"
                                    class="col-span-12 md:col-span-6 xl:col-span-3">
            @include('admin.bills.partials.client_details')
        </x-admin.show.details-block>
        <x-admin.show.details-block class="col-span-12 xl:col-span-12" :title="__('admin.Ordered items')"
                                    id="bills_ordered_items">
            @include('admin.bills.partials.ordered_items')
        </x-admin.show.details-block>
    </div>
    <div class="flex w-full justify-between px-4 mt-4">
        <div class="w-full flex justify-end">
            <x-admin.button type="link" color="text-gray-50" href="../"
                            class="text-sm focus:ring-2 font-medium rounded-lg text-gray-50 hover:underline dark:text-gray-50 px-5 py-2 flex justify-center items-center text-gray-50  bg-gray-600 hover:bg-gray-500 focus:ring-gray-400 dark:bg-gray-700
        dark:hover:bg-gray-600 dark:focus:ring-gray-900 mr-2">
                {{ __('admin.Back') }}
            </x-admin.button>
        </div>

    </div>
</x-admin.layout.admin-layout>
