<div class="xl:col-span-3" wire:init="clearInput">
    <div class="lg:grid h-full">
        <div
            class="grid grid-cols-2 grid-rows-2 absolute top-0 w-full sm:w-4/5 sm:left-[10%] xl:static xl:w-full xl:left-0">
            <div class="col-span-2 xl:col-span-1">
                <h1 class="text-dark-grey-2 text-[28px] font-medium">{{$name}}</h1>
            </div>
            <div class="row-start-2 col-start-2 xl:row-start-1">
                <div class="my-auto py-4 text-right xl:p-0">
                    <p class="text-[#2C9F68]">{{__('marketplace.product available')}}</p>
                    <p class="text-light-grey-1 text-sm font-light">{{__('marketplace.product id')}}
                        : {{$productId}}</p>
                </div>
            </div>
            <div class="row-start-2 col-start-1 py-4 xl:p-0">
                <h1 class="w-full m-auto text-[#EC3F59] text-3xl font-semibold">{{number_format($currentVariantPrice / 100, 2, ',') }} zł</h1>
            </div>
        </div>
        <form class="flex flex-col justify-between" wire:submit.prevent="addToCart">
            <div class="pb-8 xl:p-0">
                <div class="w-full mb-4">
                    <label class="text-dark-grey-1 font-light">{{__('marketplace.quantity')}}: <span
                            class="text-dark-grey-2 font-normal">10 sztuk</span></label>
                    <div
                        x-data="{isHover: false, inputValue: @entangle('quantityInput'), get hasValue() { return this.inputValue.trim() !== '' }, focusInput() { this.$refs.inputField.focus() }, checkValue(){} }"
                        class="relative py-2">
                        <input type="number" x-ref="inputField" min="10" x-model="inputValue" required wire:model="quantityInput"
                               x-bind:class="isHover ? 'border-[#BABFCA]' : 'border-[#E5E7EB]'"
                               x-on:input="checkValue"
                               class="p-2 w-full border border-[#E5E7EB] rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-[0_0_0_2px_rgba(107,148,226,0.2)] disabled:border-[#E5E7EB]"
                        >
                        <div x-show="!hasValue" x-on:click="focusInput()" x-on:mouseenter="isHover = true" x-on:mouseleave="isHover = false"
                             class="absolute top-5 left-2 text-xs  sm:text-base sm:top-4">
                            <span class="text-dark-grey-2">{{__('marketplace.enter product quantity')}}</span> <span
                                class="text-dark-grey-1 font-light">({{__('marketplace.minimum 10 pieces')}})</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="w-full mb-4">
                        <label class="text-dark-grey-1 font-light">{{__('marketplace.variant')}}: <span
                                class="text-dark-grey-2 font-normal">{{$currentVariantName}}</span></label>
                        <div class="w-full grid grid-cols-3 gap-2 justify-between py-2">
                            @foreach($variants as $variant)
                                <button type="button" wire:key="$variant->code"
                                        {{ ($variant->inStock) ? '' : 'disabled' }} wire:click="changeVariant('{{$variant->code}}')"
                                        class="text-dark-grey-2 border w-full rounded py-2 px-4 disabled:border-[#E5E7EB] disabled:text-light-grey-1
                                        {{ $currentVariantCode === $variant->code ? 'border-[#EC3F59] shadow-[0_0_0_2px_rgba(236,63,89,0.2)]' : 'border-[#E5E7EB] hover:border-[#BABFCA]' }}">
                                    {{$variant->name}}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div x-data="{isVisible: false}" class="relative my-8 xl:m-0">
                <div x-show="isVisible" x-transition class="absolute bottom-16 rounded text-center border-2 border-[#2C9F68] p-2 text-[#2C9F68] w-full">
                    Produkt dodany do koszyka
                </div>
                <button type="submit" x-on:click="isVisible = true; setTimeout(() => isVisible = false, 3000)"
                        class="w-full py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59] text-xl font-medium xl:m-0">
                    {{__('marketplace.add to cart')}}
                </button>
            </div>
        </form>
    </div>
</div>

