<x-admin.layout.admin-layout>
    <div class="grid gap-5 p-5">
        <div
            class="col-span-5 bg-gray-100 text-center  w-full items-center rounded-lg p-2 text-2xl text-gray-600 dark:text-gray-300 mb-2">
            <div
                class="text-center w-full items-center rounded-lg p-2 text-2xl text-gray-600 dark:text-gray-300 mb-2">
                <h1>{{__('marketplace.products')}}</h1>
            </div>
        </div>
        <div>
            <div class="grid grid-cols-12 md:grid-cols-3 lg:grid-cols-5 gap-4 relative">
                @foreach($products as $product)
                    <x-admin.marketplace.product-card :code="$product->code" :id="$product->id"
                                                      :images="$product->images"
                                                      :name="$product->name" />
                @endforeach
            </div>
        </div>
    </div>
</x-admin.layout.admin-layout>
