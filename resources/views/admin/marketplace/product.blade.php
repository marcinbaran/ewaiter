<x-admin.layout.admin-layout>
    {{--    @dump($data)--}}
    {{--    <div class="gap-4 h-full w-full">--}}
    {{--        <div class="flex flex-col gap-2 ">--}}
    {{--            @foreach($images as $image)--}}
    {{--                <div class=" dark:text-primary-700 dark:hover:text-primary-700 m-10">--}}
    {{--                    <div class="">--}}
    {{--                        <img src="{{ $image }}" class="w-1/4 h" alt="Product Image">--}}
    {{--                    </div>--}}
    {{--                    <div class="flex">--}}
    {{--                    <span class="flex">--}}
    {{--                        <p class="text-center"><b>{{__('marketplace.average grade')}}</b> {{$data->averageRating}}</p>--}}
    {{--                    </span>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            @endforeach--}}
    {{--        </div>--}}
    {{--        <div class="flex flex-col gap-2">--}}
    {{--            <div class="flex items-center justify-center dark:text-primary-700 dark:hover:text-primary-700">--}}
    {{--                <h1 class="text-2xl font-bold text-center  inline-block rounded-full p-2">{{$data->name}}</h1>--}}
    {{--            </div>--}}

    {{--            <div class="flex dark:text-primary-700 dark:hover:text-primary-700 dark:bg-gray-800">--}}
    {{--                <div class="w-1/2 bg-gray-100 dark:bg-gray-800">--}}
    {{--                    <div class="p-5">--}}
    {{--                        <p>--}}
    {{--                            <b>{{__('marketplace.description')}}:</b>--}}
    {{--                            <br>--}}
    {{--                            <span id="description">{{$data->shortDescription}}</span>--}}
    {{--                            <a href="#" id="more" class="text-blue-500 hover:underline">{{__('marketplace.more')}}</a>--}}
    {{--                        </p>--}}
    {{--                    </div>--}}
    {{--                    <div class="flex justify-center items-center mt-2">--}}
    {{--                        <x-admin.marketplace.quantity-card :variants="$variants" />--}}
    {{--                    </div>--}}

    {{--                    <br>--}}
    {{--                    <br>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    {{--    {{dd($data, $variants)}}--}}
    <div class="relative pt-40 sm:px-4 sm:pb-4 lg:mx-4 xl:p-8">
        <div class="grid grid-rows-1 border-b border-[#F3F4F6] gap-8 py-4 lg:pb-8 xl:grid-cols-5">
            <div x-data="{ currentImg: '{{$images[0]}}' }" class="xl:col-span-2 xl:px-1">
                <div class="grid grid-cols-5 gap-2 w-4/5 m-auto md:gap-4 md:px-4 xl:w-full xl:h-full">
                    <div class="flex flex-col gap-2">
                        @foreach($images as $image)
                            <div class="max-w-28" x-on:click="currentImg = '{{$image}}'; console.log('img changed')">
                                <img class="rounded w-full h-full" src="{{$image}}" alt="{{$data->name}}" />
                            </div>
                        @endforeach
                    </div>
                    <div class="col-span-4 max-w-lg">
                        <img class="rounded-md w-full" :src="currentImg" alt="{{$data->name}}" />
                    </div>
                </div>
            </div>

            <livewire:marketplace.product-content :id="$data->id" :variants="$variants" :name="$data->name" />

        </div>
        <div>
            <div class="w-full py-4">
                <h2 class="text-dark-grey-1 text-xl py-4">{{__('marketplace.product description')}}</h2>
                <p class="text-dark-grey-2 font-light text-justify">{{ $data->description }}</p>
            </div>
        </div>

{{--        <a href="{{route('admin.marketplace.cart', "cart")}}"--}}
{{--           class="fixed text-[#EC3F59] bg-[#E5E7EB] border-[#E5E7EB] bottom-20 right-1 text-center py-2 rounded-2xl w-20 md:bottom-[2.5%] md:right-[2.5%] xl:bottom-[5%] xl:right-[3%] ">--}}
{{--            <div class="py-2 px-4">--}}
{{--                <svg class="mx-auto" width="32" height="37" viewBox="0 0 32 37" fill="none"--}}
{{--                     xmlns="http://www.w3.org/2000/svg">--}}
{{--                    <path--}}
{{--                            d="M10.1317 16.5556V6.83333C10.1317 5.28624 10.7499 3.80251 11.8502 2.70854C12.9505 1.61458 14.4428 1 15.9989 1C17.555 1 19.0474 1.61458 20.1477 2.70854C21.248 3.80251 21.8661 5.28624 21.8661 6.83333V16.5556M4.91194 10.7222H27.0881C27.652 10.7222 28.2093 10.8433 28.7218 11.0775C29.2343 11.3116 29.6897 11.6531 30.057 12.0787C30.4242 12.5042 30.6946 13.0037 30.8495 13.5428C31.0044 14.0819 31.0403 14.648 30.9546 15.2022L28.5001 31.0533C28.287 32.4309 27.5849 33.687 26.5209 34.5944C25.4568 35.5018 24.1012 36.0004 22.6994 36H9.29866C7.89718 35.9999 6.54201 35.5011 5.47838 34.5938C4.41475 33.6864 3.71293 32.4305 3.4999 31.0533L1.04545 15.2022C0.959726 14.648 0.995556 14.0819 1.15048 13.5428C1.30541 13.0037 1.57577 12.5042 1.94303 12.0787C2.31028 11.6531 2.76575 11.3116 3.2782 11.0775C3.79066 10.8433 4.34797 10.7222 4.91194 10.7222Z"--}}
{{--                            stroke="#EC3F59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />--}}
{{--                </svg>--}}
{{--            </div>--}}
{{--            <p>{{__('marketplace.see your cart')}}</p>--}}
{{--        </a>--}}

    </div>
</x-admin.layout.admin-layout>

{{--<script>--}}
{{--    document.getElementById("more").addEventListener("click", function(event) {--}}
{{--        event.preventDefault();--}}
{{--        let descriptionElement = document.getElementById("description");--}}
{{--        let moreElement = document.getElementById("more");--}}

{{--        if (moreElement.textContent === "{{__('marketplace.more')}}") {--}}
{{--            descriptionElement.textContent = `{{ $data->description }}`;--}}
{{--            moreElement.textContent = "{{__('marketplace.less')}}";--}}
{{--        } else {--}}
{{--            descriptionElement.textContent = `{{ $data->shortDescription }}`;--}}
{{--            moreElement.textContent = "{{__('marketplace.more')}}";--}}
{{--        }--}}
{{--    });--}}
{{--</script>--}}
