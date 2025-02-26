<div class="my-6 flex w-full flex-col gap-6">
    <div>
        <x-admin.form.label for="promotion_type" :required="true">{{ __('admin.Type') }}</x-admin.form.label>
        <x-admin.form.select id="promotion_type" class="!bg-gray-100 dark:!bg-gray-500" name="type" wire:model="promotionType">
            <option value="0" {{ $promotionType == 0 ? 'selected' : '' }}>
                {{ __('admin.On dish') }}
            </option>
            <option value="2" {{ $promotionType == 2 ? 'selected' : '' }}>
                {{ __('admin.On category') }}
            </option>
            <option value="3" {{ $promotionType == 3 ? 'selected' : '' }}>
                {{ __('admin.On bundle') }}
            </option>
        </x-admin.form.new-input>
    </div>
    <div>
        <x-admin.form.label for="promotion_value" :required="true">{{ __('admin.Value') }}</x-admin.form.label>
        <div class="flex" x-data="{ valueType: @entangle('valueType') }">
            <div class="flex-1" x-show="valueType == 0">
                <x-admin.form.new-input containerClass="rounded-r-none" type="percent" id="promotion_percent_value"
                    name="value" value="{{ $value }}" x-bind:disabled="valueType === 1"
                    x-bind:required="valueType === 1" />
            </div>
            <div class="flex-1" x-show="valueType == 1">
                <x-admin.form.new-input type="money" containerClass="rounded-r-none" id="promotion_money_value"
                    name="value" value="{{ $value }}" x-bind:disabled="valueType === 0"
                    x-bind:required="valueType === 0" />
            </div>
            <x-admin.form.select class="!w-auto rounded-l-none" name="typeValue" :required="true" x-model="valueType">
                <option value="0" {{ $valueType == 0 ? 'selected' : '' }}>%</option>
                <option value="1" {{ $valueType == 1 ? 'selected' : '' }}>PLN</option>
            </x-admin.form.select>
        </div>
    </div>
    @if ($promotionType == 0)
        <div class="promotion_orderDish mb-6 flex flex-col">
            <x-admin.form.label for="promotion_orderDish"
                :required="true">{{ __('admin.The product on which we are doing the promotion') }}</x-admin.form.label>
            <x-admin.form.new-input type="select" id="promotion_orderDish" name="orderDish[id]" :required="true"
                :value="route('admin.promotions.dishes', ['id' => $promotionId])" />
            {{-- <x-admin.form.select class="select2-dish select2-withImage" id="promotion_orderDish" name="orderDish[id]"
                data-base="dishes" data-value="{{ $orderDishId ?? '' }}" role="select2" :required="true" /> --}}
        </div>
    @elseif ($promotionType == 2)
        <div class="promotion_orderCategory mb-6 flex flex-col">
            <x-admin.form.label for="promotion_orderCategory"
                value="{{ __('admin.The food category on which we are doing the promotion') }}" :required="true" />
            <x-admin.form.new-input type="select" id="promotion_orderCategory" name="orderCategory[id]" :value="route('admin.promotions.categories', ['id' => $promotionId])" :required="true" />
            {{-- <x-admin.form.select class="select2-category" id="promotion_orderCategory" name="orderCategory[id]"
                data-base="food-categories" data-value="{{ $orderCategoryId ?? '' }}" role="select2"
                :required="true" /> --}}
        </div>
    @elseif ($promotionType == 3)
        <div class="flex flex-col">
            <x-admin.form.label for="promotion_orderDishes"
                :required="true">{{ __('admin.The products on which we are doing the promotion') }}</x-admin.form.label>
            <x-admin.form.select class="select2-bundle" id="promotion_orderDishes" name="orderDishes[][id]"
                data-base="dishes" data-s2="true" data-value="{{ $dishes }}" role="select2" multiple="multiple"
                :required="true" />
        </div>
    @endif
    <x-admin.form.tablist id="myTabContent" :locales="$locales">
        @foreach ($locales as $locale)
            <div class="hidden space-y-6 rounded-lg rounded-t-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                <div class="group relative z-0 w-full">
                    <x-admin.form.label for="promotion_description_{{ $locale }}"
                        value="{{ __('admin.Description') }}" />
                    <x-admin.form.new-input type="textarea" id="promotion_description_{{ $locale }}"
                        name="description[{{ $locale }}]" value="{{ $descriptions[$locale] ?? '' }}"
                        min="3" max="1000" />
                </div>
            </div>
        @endforeach
    </x-admin.form.tablist>
    @if ($promotionType == 3)
        <div>
            <x-admin.form.label for="promotion_photo" :required="true">{{ __('admin.File') }}</x-admin.form.label>
            <x-admin.form.gallery name="photo" namespace="promotions" data-allow-reorder="true" :files="$photos"
                multiple="false" accept="image/png, image/jpeg, image/gif" :id="$promotionId" :required="true"
                requiredText="{{ __('validation.image_required') }}" requiredSingleText="{{ __('validation.single_image_required') }}" />
        </div>
    @endif
    <div class="promotion-dates-container justify-space-between flex gap-6">
        <div class="flex-1">
            <x-admin.form.label for="promotion_startAt">{{ __('admin.Start date') }}</x-admin.form.label>
            <x-admin.form.new-input type="date" id="promotion_startAt" name="startAt"
                value="{{ $times['start'] ? \Carbon\Carbon::parse($times['start'])->format('Y-m-d') : '' }}"
                placeholder="Od dnia" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" />
        </div>
        <div class="flex-1">
            <x-admin.form.label for="promotion_endAt">{{ __('admin.End date') }}</x-admin.form.label>
            <x-admin.form.new-input type="date" id="promotion_endAt" name="endAt"
                value="{{ $times['end'] ? \Carbon\Carbon::parse($times['end'])->format('Y-m-d') : '' }}"
                placeholder="Do dnia" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" />
        </div>
    </div>
    <div class="justify-space-between flex gap-6">
        <div class="flex-1">
            <x-admin.form.new-input type="toggle" id="promotion_merge" name="merge" :checked="$isPromotionOverlap"
                :placeholder="__('admin.Will the promotion of links with others?')" />
        </div>
        <div class="flex-1">
            <x-admin.form.new-input type="toggle" id="promotion_active" name="active" :checked="$isPromotionActive"
                :placeholder="__('admin.Is the promotion active?')" />
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.dispatchEvent(new Event('add-rounded-corners-to-new-inputs'));
        }, 1000);
    </script>
</div>
