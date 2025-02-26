<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.promotions.update', ['promotion' => $data->id]) : route('admin.promotions.store');
        $oldValues = [
            'value' => old('value', $data->value),
            'typeValue' => old('typeValue', $data->type_value),
            'orderDishes' => $oldDishes,
            'startAt' => old('startAt', $data->start_at ? \Carbon\Carbon::parse($data->start_at)->format('Y-m-d') : ''),
            'endAt' => old('endAt', $data->end_at ? \Carbon\Carbon::parse($data->end_at)->format('Y-m-d') : ''),
            'merge' => old('merge', $data->merge),
            'active' => old('active', $data->active),
        ];
    @endphp
    <x-admin.form.form id="tags" formWide="w-1/2" role="form" method="POST" :redirectUrl="$redirectUrl"
                       :action="$action"
                       enctype="multipart/form-data" class="flex flex-col gap-6">
        <div class="promotion-type-tabs">
            @if (!$data->id)
                <ul class="flex gap-6 text-gray-600 dark:text-gray-400">
                    <li class="flex-1">
                        <a href="{{route('admin.promotions.create.dish')}}"
                           class="p-2 flex items-center justify-center w-full h-full hover:text-gray-900 dark:hover:text-gray-50 border-b-2 border-transparent">
                            {{ __('admin.On dish') }}
                        </a>
                    </li>
                    <li class="flex-1">
                        <a href="{{route('admin.promotions.create.category')}}"
                           class="p-2 flex items-center justify-center w-full h-full hover:text-gray-900 dark:hover:text-gray-50 border-b-2 border-transparent">
                            {{ __('admin.On category') }}
                        </a>
                    </li>
                    <li class="flex-1">
                        <a href="{{route('admin.promotions.create.bundle')}}"
                           class="p-2 flex items-center justify-center w-full h-full text-primary-900 dark:text-primary-700 border-b-2 border-primary-900 dark:border-primary-700">
                            {{ __('admin.On bundle') }}
                        </a>
                    </li>
                </ul>
            @endif
        </div>
        <x-admin.form.tablist id="myTabContent" :locales="$data->getLocales()">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-t-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div class="group relative z-0 w-full mb-6">
                        <x-admin.form.label for="promotion_name_{{ $locale }}" value="{{ __('admin.Name') }}"
                                            :required="$locale === 'pl'" />
                        <x-admin.form.new-input type="text" id="promotion_name_{{ $locale }}" name="name[{{ $locale }}]"
                                                value="{{  old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                min="3" max="100" :required="$locale === 'pl'" />
                    </div>
                    @if(!empty(array_intersect($user_roles, config('options_visibility.promotionController')[0]['roles'])))
                        <div class="group relative z-0 w-full">

                            <x-admin.form.label for="promotion_description_{{ $locale }}"
                                                value="{{ __('admin.Description') }}" />
                            <x-admin.form.new-input type="textarea" id="promotion_description_{{ $locale }}"
                                                    name="description[{{ $locale }}]"
                                                    value="{{  old('description.' . $locale, $data->getTranslation('description', $locale)) }}"
                                                    min="3" max="1000" />
                        </div>
                    @endif
                </div>
            @endforeach
        </x-admin.form.tablist>
        <div>
            <input type="hidden" name="type" value="{{ \App\Enum\PromotionType::BUNDLE }}">
            <input type="hidden" name="typeValue" value="{{ \App\Enum\PromotionValueType::PRICE }}">
            <x-admin.form.label for="promotion_value"
                                :required="true">{{ __('promotion.bundle_price') }}</x-admin.form.label>
            <x-admin.form.new-input type="money" containerClass="rounded-r-none" id="promotion_value"
                                    name="value" value="{{ $oldValues['value'] }}" />
        </div>
        <div>
            <x-admin.form.label for="promotion_orderDishes"
                                :required="true">{{ __('admin.The products on which we are doing the promotion') }}</x-admin.form.label>
            <x-admin.form.new-input type="select" id="promotion_orderDishes" name="orderDishes[][id]"
                                    :value="route('admin.promotions.dishes', ['id' => $data->id])" :required="true"
                                    mode="multiple" :oldValue="$oldValues['orderDishes']" />
        </div>
        <div>
            <x-admin.form.label for="promotion_photo">{{ __('admin.File') }}</x-admin.form.label>
            <x-admin.form.gallery
                :id="$data->id"
                :files="$data->photos_json"
                name="photo"
                accept="image/png, image/jpeg, image/jpg, image/webp"
                namespace="promotions"
                :required="false"
            />
        </div>
        <div class="promotion-dates-container justify-space-between flex flex-col 2xl:flex-row gap-6">
            <div class="flex-1">
                <x-admin.form.label for="promotion_startAt">{{ __('admin.Start date') }}</x-admin.form.label>
                <x-admin.form.new-input type="date" id="promotion_startAt" name="startAt"
                                        value="{{ $oldValues['startAt'] }}"
                                        placeholder="Od dnia" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" />
            </div>
            <div class="flex-1">
                <x-admin.form.label for="promotion_endAt">{{ __('admin.End date') }}</x-admin.form.label>
                <x-admin.form.new-input type="date" id="promotion_endAt" name="endAt"
                                        value="{{ $oldValues['endAt'] }}"
                                        placeholder="Do dnia" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" />
            </div>
        </div>
        <div class="flex-1">
            <x-admin.form.new-input type="toggle" id="promotion_active" name="active"
                                    :checked="$oldValues['active']"
                                    :placeholder="__('admin.Is the promotion active?')" />
        </div>
    </x-admin.form.form>

</x-admin.layout.admin-layout>
