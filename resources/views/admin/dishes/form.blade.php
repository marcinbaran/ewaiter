<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.dishes.update', ['dish' => $data->id]) : route('admin.dishes.store');
    @endphp

    <x-admin.form.form role="form" id="dishesForm" id="dish" method="POST" :redirectUrl="$redirectUrl" :action="$action"
                       enctype="multipart/form-data" formWide="w-1/2" class="flex flex-col gap-6">
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" for="dish_name_{{ $locale }}"
                                            value="{{ __('admin.dishes.name') }}" :required="$locale == 'pl'" />
                        <x-admin.form.new-input type="text"
                                                name="{{ 'name[' . $locale . ']' }}"
                                                id="dish_name_{{ $locale }}"
                                                :required="$locale == 'pl'"
                                                value="{{old('name.'. $locale, $data->getTranslation('name', $locale)) }}"
                                                min="3" max="100" />
                    </div>
                    <div>
                        <x-admin.form.label for="dish_description_{{ $locale }}"
                                            value="{{ __('admin.dishes.description') }}" />
                        <x-admin.form.new-input type="textarea"
                                                name="{{'description[' . $locale . ']' }}"
                                                id="dish_description_{{ $locale }}"
                                                value="{{old('description.'. $locale, $data->getTranslation('description', $locale)) }}"
                                                min="3" max="1000" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
        <div>
            <x-admin.form.label value="{{ __('admin.Photo') }}" :required="true" />
            <x-admin.form.gallery
                :id="$data->id"
                :files="$data->photos_json"
                name="photo"
                accept="image/png, image/jpeg, image/jpg, image/webp"
                namespace="dishes"
                :required="false"
            />
        </div>
        <div class="flex flex-col sm:flex-row gap-6">
            <div class="min-w-0 flex-1 flex flex-col justify-between">
                <x-admin.form.label for="additions_position" value="{{ __('admin.dishes.position') }}" />
                <x-admin.form.new-input type="number" name="position" id="additions_position"
                                        value="{{ old('position', $data->position) }}" step='1' min='0' />
            </div>
            <div class="min-w-0 flex-1 flex flex-col justify-between">
                <x-admin.form.label value="{{ __('admin.dishes.price') }}" for="dish_price" :required="true" />
                <x-admin.form.new-input type="money" name="price" id="dish_price" :required="true" min="0"
                                        max="999999.99"
                                        value="{{ old('price', $data->price) }}" />
            </div>
            @if(config('options_visibility.dishController')[0]['isWebsite'] == 1 &&@!empty(array_intersect(config('options_visibility.dishController')[0]['roles'], $user_roles)))
                <div class="min-w-0 flex-1 flex flex-col justify-between">
                    <x-admin.form.label value="{{ __('admin.dishes.wait_time') }}" for="dish_timeWait"
                                        :required="true" />
                    <x-admin.form.new-input type="number" name="timeWait" id="dish_timeWait" :required="true"
                                            value="{{ old('timeWait', $data->time_wait) }}" min="0" step="1"
                                            prefix="min" />
                </div>
            @endif
        </div>
        @if(config('options_visibility.dishController')[0]['isWebsite'] == 1 &&@!empty(array_intersect(config('options_visibility.dishController')[1]['roles'], $user_roles)))
            <div>
                <x-admin.form.label for="dish_attributes" value="{{ __('admin.dishes.attributes') }}" />
                <x-admin.form.new-input type="select" mode="multiple" id="dish_attributes" name="dish_attributes[][id]"
                                        oldValue="{{ $oldAttributes }}"
                                        value="{{ route('admin.dishes.attributes', ['id' => $data->id]) }}" />
            </div>
        @endif
        <div>
            <x-admin.form.label for="dish_labels" value="{{ __('admin.dishes.labels') }}" />
            <x-admin.form.new-input type="select" mode="multiple" id="dish_labels" name="labels[][id]"
                                    oldValue="{{ $oldLabels }}"
                                    value="{{ route('admin.dishes.labels', ['id' => $data->id]) }}" />
        </div>
        <div>
            <x-admin.form.label for="dish_allergens" value="{{ __('admin.dishes.allergens') }}" />
            <x-admin.form.new-input type="select" mode="multiple" id="dish_allergens" name="tags[][id]"
                                    oldValue="{{ $oldTags }}"
                                    value="{{ route('admin.dishes.tags', ['id' => $data->id]) }}" />
        </div>
        <div>
            <x-admin.form.label for="additions_groups" value="{{ __('admin.dishes.addition_groups') }}" />
            <x-admin.form.new-input type="select" mode="multiple" id="additions_groups" name="additions_groups[][id]"
                                    oldValue="{{ $oldAdditionGroups }}"
                                    value="{{ route('admin.dishes.additionGroups', ['id' => $data->id]) }}" />
        </div>
        <div>
            <x-admin.form.label for="dish_category" value="{{ __('admin.dishes.category') }}" :required="true" />
            <x-admin.form.new-input type="select" id="dish_category" name="category[id]"
                                    oldValue="{{ $oldCategories }}"
                                    value="{{ route('admin.dishes.categories', ['id' => $data->id]) }}" />
        </div>
        <div>
            <x-admin.form.label class="pb-2" value="{{ __('admin.dishes.availability_in_specific_days') }}"
                                :required="true" />
            <x-admin.form.weekdayspicker id="availability" name="availability" :data="$data" />
        </div>
        <div>
            <x-admin.form.label value="{{ __('admin.dishes.availability_in_specific_hours') }}" />
            <div class="flex gap-6">
                <div class="min-w-0 flex-1">
                    <x-admin.form.new-input type="time" name="availability[start_hour]" placeholder="Od godziny"
                                            value="{{ old('availability.start_hour', $data->availability?->start_hour ? \Carbon\Carbon::parse($data->availability['start_hour'])->format('H:i') : null) }}" />
                </div>
                <div class="min-w-0 flex-1">
                    <x-admin.form.new-input type="time" name="availability[end_hour]" placeholder="Do godziny"
                                            value="{{ old('availability.end_hour', $data->availability?->end_hour ? \Carbon\Carbon::parse($data->availability['end_hour'])->format('H:i') : null) }}" />
                </div>
            </div>
        </div>
        <div class="flex gap-6">
            <div class="min-w-0 flex-1">
                <x-admin.form.toggle id="dish_visibility" name="visibility"
                                     checked="{{ old('visibility', isset($data->category) && $data->visibility) }}">
                    {{ __('admin.dishes.is_dish_visible') }}</x-admin.form.toggle>
            </div>
            <div class="min-w-0 flex-1">
                <x-admin.form.toggle id="dish_delivery" name="delivery"
                                     checked="{{ old('delivery', $data->delivery) }}">
                    {{ __('admin.dishes.is_dish_available_to_delivery') }}</x-admin.form.toggle>
            </div>
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
