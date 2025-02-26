<x-admin.layout.admin-layout>
    @php
        $action = ($data->id > 0)?
            route('admin.restaurant_tags.update', ['restaurant_tag' => $data->id]) : route('admin.restaurant_tags.store');
    @endphp
    <x-admin.form.form
        role="form"
        id="restaurantTagsForm"
        method="POST"
        :redirectUrl="$redirectUrl"
        :action="$action"
        enctype="multipart/form-data"
        formWide="w-1/2"
        class="flex flex-col gap-6"
    >
        <div>
            <x-admin.form.label for="restaurant_tag" value="{{ __('admin.Key') }}" name="key" :required="true" />
            <x-admin.form.new-input type="text" id="restaurant_tag" name="key" value="{{ old('key', $data->key) }}"
                                    min="3" max="35" :required="true" />
        </div>
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label value="{{ __('admin.Name') }}" for="restaurant_tag_name_{{ $locale }}"
                                            :required="$locale == 'pl'" />
                        <x-admin.form.new-input type="text"
                                                name="{{ $locale == 'pl' ? 'name' : 'name_locale[' . $locale . ']' }}"
                                                id="restaurant_tag_name_{{ $locale }}"
                                                value="{{ old($locale == 'pl' ? 'name' : 'name_locale.' . $locale, $data->value[$locale]?? '') }}"
                                                :required="$locale == 'pl'" min="3" max="100" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
