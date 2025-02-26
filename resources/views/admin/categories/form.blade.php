<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.categories.update', ['foodCategory' => $data->id]) : route('admin.categories.store');
    @endphp
    <x-admin.form.form role="form" id="categories" method="POST" enctype="multipart/form-data" :action="$action"
                       :redirectUrl="$redirectUrl" formWide="w-1/2" class="flex flex-col gap-6">
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label value="{{ __('admin.Name') }}"
                                            for="category_name_{{ $locale }}" :required="$locale == 'pl'" />
                        <x-admin.form.new-input id="category_name_{{ $locale }}"
                                                name="{{ 'name[' . $locale . ']' }}"
                                                type="text"
                                                value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                containerClass="flex-1" min="3" max="100" :required="$locale == 'pl'" />
                    </div>
                    <div>
                        <x-admin.form.label value="{{ __('admin.Description') }}"
                                            for="category_description_{{ $locale }}" />
                        <x-admin.form.new-input type="textarea" id="category_description_{{ $locale }}"
                                                name="{{ 'description[' . $locale . ']' }}"
                                                min="3" max="1000"
                                                value="{{ old('description.' . $locale, $data->getTranslation('description', $locale)) }}" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
        @if(isset($parent))
            <input type="hidden" name="parent[id]" value="{{ $parent }}">
        @else
            <div>
                <x-admin.form.label value="{{ __('admin.Parent') }}" for="category_parent"></x-admin.form.label>
                <x-admin.form.new-input type="select" name="parent[id]" id="category_parent" :oldValue="$oldCategories"
                                        value="{{ route('admin.categories.categories', ['id' => $data->id]) }}" />
            </div>
        @endif
        <div>
            <x-admin.form.label value="{{ __('admin.Position') }}" for="category_position"></x-admin.form.label>
            <x-admin.form.new-input type="number" name="position" id="category_position"
                                    value="{{ old('position', $data->position) }}" min="0" max="1000" />
        </div>
        @foreach (config('options_visibility.foodCategoryController') as $option)
            @if (!empty(array_intersect($option['roles'], $user_roles)))
                <div>
                    <x-admin.form.label value="{{ __('admin.Photo') }}" :required="false"></x-admin.form.label>
                    <x-admin.form.gallery
                        :id="$data->id"
                        :files="$data->photos_json"
                        name="photo"
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        namespace="food_categories"
                        :required="false"
                        aspectRatio="rectangle"
                    />
                </div>
            @endif
        @endforeach
        <div>
            <x-admin.form.label class="pb-2" value="{{ __('admin.Availability on specific days') }}" />
            <x-admin.form.weekdayspicker name="availability" id="availability" :data="$data" />
        </div>
        <div>
            <x-admin.form.label value="{{ __('admin.Availability on specific hours') }}" />
            <div class="flex gap-6">
                <div class="min-w-0 flex-1">
                    <x-admin.form.new-input type="time" name="availability[start_hour]" placeholder="Od godziny"
                                            value="{{ old('availability[start_hour]', $data->availability?->start_hour ? \Carbon\Carbon::parse($data->availability->start_hour)->format('H:i') : '') }}" />
                </div>
                <div class="min-w-0 flex-1">
                    <x-admin.form.new-input type="time" name="availability[end_hour]" placeholder="Do godziny"
                                            value="{{ old('availability[end_hour]', $data->availability?->end_hour ? \Carbon\Carbon::parse($data->availability->end_hour)->format('H:i') : '') }}" />
                </div>
            </div>
        </div>
        <div>
            <x-admin.form.toggle name="visibility" id="category_visibility"
                                 :checked="old('visibility', $data->visibility)">{{ __('admin.Is the category visible?') }}</x-admin.form.toggle>
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
