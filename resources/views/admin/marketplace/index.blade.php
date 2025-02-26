<x-admin.layout.admin-layout>
    <div class="grid grid-cols-6 gap-4 ml-[-1.25rem] calc-width font-['Albert_Sans']">

        {{--        <div class="col-span-1">--}}
        {{--            <div--}}
        {{--                class="bg-gray-100 text-center  w-full items-center rounded-lg p-2 text-2xl text-gray-600 dark:text-gray-400">--}}
        {{--                <h1>{{__('marketplace.categories')}}</h1>--}}
        {{--            </div>--}}
        {{--            <div class="grid grid-cols-1 gap-5 p-5">--}}
        {{--                @foreach($data as $taxon)--}}
        {{--                    <div class="group">--}}
        {{--                        <a href="{{route('admin.marketplace.products', ['code' => $taxon->code])}}"--}}
        {{--                           class="flex flex-col p-4 text-center bg-gray-200 border border-gray-300 rounded-lg shadow-md transform transition-transform duration-500 hover:-translate-y-1 hover:border-red-500">{{$taxon->name}}</a>--}}
        {{--                        <div class="hidden group-hover:block">--}}
        {{--                            @foreach($taxon->children as $subCategory)--}}
        {{--                                <div>--}}
        {{--                                    <a href="{{route('admin.marketplace.products', ['code' => $subCategory->code])}}"--}}
        {{--                                       class="flex flex-col p-4 text-center bg-gray-100 border border-gray-300 rounded-lg shadow-md transform transition-transform duration-500 hover:-translate-y-1 hover:border-red-500 mt-5 mb-5">{{$subCategory->name}}</a>--}}
        {{--                                </div>--}}
        {{--                            @endforeach--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                @endforeach--}}
        {{--            </div>--}}
        {{--        </div>--}}

        <div x-data="{isTile: true}" class="col-span-full xl:col-span-4">
            <div class="flex justify-between px-4 py-6">
                <div>
                    <h1 class="text-2xl font-medium text-dark-grey-2">Marketplace</h1>
                    <p class="font-light text-xl text-dark-grey-1">{{__('marketplace.newest products')}}</p>
                </div>
                <div class="flex gap-2">
                    <x-admin.marketplace.category-list-collapsible />
                    <x-admin.marketplace.toggle-tile-list x-on:click="isTile = !isTile" />
                </div>
            </div>
            <x-admin.marketplace.products :products="$latestProducts" />

            {{--            <div x-bind:class="{ 'md:grid-cols-2' : isTile, 'grid-cols-1' : !isTile}"--}}
            {{--                 class="grid grid-cols-1 gap-4 px-4">--}}
            {{--                @foreach($latestProducts as $product)--}}
            {{--                    <x-admin.marketplace.product-card--}}
            {{--                        :code="$product->code" :id="$product->id"--}}
            {{--                        :images="$product->images"--}}
            {{--                        :name="$product->name"--}}
            {{--                        :shortDescription="$product->shortDescription"--}}
            {{--                        :description="$product->description" />--}}
            {{--                @endforeach--}}
            {{--            </div>--}}
        </div>
        <div
            class="border-l border-[#E5E7EB] relative col-span-2 hidden xl:block before:content[''] before:w-px before:absolute before:left-[-1px] before:bottom-full before:h-full before:bg-[#E5E7EB]
            after:absolute after:w-px after:left-[-1px] after:top-8 after:content-[''] after:h-full after:bg-[#E5E7EB]">
            <livewire:marketplace.mini-cart-card />
        </div>
    </div>
</x-admin.layout.admin-layout>
