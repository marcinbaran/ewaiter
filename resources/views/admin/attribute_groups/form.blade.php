<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.attribute_groups.update', ['attribute_group' => $data->id]) : route('admin.attribute_groups.store');
    @endphp

    <x-admin.form.form
        role="form"
        id="attribute_groups"
        method="POST"
        :action="$action"
        enctype="multipart/form-data"
        :redirectUrl="$redirectUrl"
        formWide="w-1/2"
        class="flex flex-col gap-6"
    >
        @csrf
        <input type="hidden" name="attribute_ids" value="{{$attributes}}">
        <input class="input-slug" type="hidden" name="key" value="{{ old('key', $data->key) }}"
               data-slug-for="attribute_group_name_pl" />
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" value="{{ __('admin.Name') }}"
                                            for="attribute_group_name_{{ $locale }}" :required="$locale == 'pl'" />
                        <x-admin.form.new-input type="text"
                                                name="{{ 'name[' . $locale . ']' }}"
                                                id="attribute_group_name_{{ $locale }}" type="text"
                                                :required="$locale == 'pl'"
                                                value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                min="3" max="50" />
                    </div>
                    <div>
                        <x-admin.form.label for="attribute_group_description_{{ $locale }}"
                                            value="{{ __('admin.dishes.description') }}" />
                        <x-admin.form.new-input type="textarea"
                                                name="{{ 'description[' . $locale . ']' }}"
                                                id="attribute_group_description_{{ $locale }}"
                                                value="{{ old('description.' . $locale, $data->getTranslation('description', $locale)) }}"
                                                min="3" max="1000" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
        <div>
            <x-admin.form.label value="{{__('attribute_groups.input_type')}}" for="input_type" :required="true" />
            <x-admin.form.new-input type="select" name="input_type" id="input_type" mode="single"
                                    :value="route('admin.attribute_groups.input_types',['id' => $data->id])"
                                    :oldValue="$oldInputType" />
        </div>
        <div class="flex flex-col 2xl:flex-row gap-6 justify-center">
            <x-admin.form.new-input type="toggle" name="is_active" id="is_active"
                                    checked="{{ old('is_active', $data->is_active) }}"
                                    :placeholder="__('attribute_groups.is_attribute_group_active')" />
            <x-admin.form.new-input type="toggle" name="is_primary" id="is_primary"
                                    :placeholder="__('attribute_groups.is_attribute_group_primary')"
                                    checked="{{ old('is_primary', $data->is_primary) }}" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
