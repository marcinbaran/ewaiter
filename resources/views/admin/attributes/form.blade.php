<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.attributes.update', ['attribute' => $data->id]) : route('admin.attributes.store');
    @endphp

    <x-admin.form.form
        role="form"
        id="attributes"
        method="POST"
        :action="$action"
        enctype="multipart/form-data"
        :redirectUrl="$redirectUrl"
        formWide="w-1/2"
        class="flex flex-col gap-6"
    >
        @csrf
        <input class="input-slug" type="hidden" name="key" value="{{ old('key', $data->key) }}"
               data-slug-for="attribute_name_pl" />
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" value="{{ __('admin.Name') }}"
                                            for="attribute_name_{{ $locale }}" :required="$locale == 'pl'" />
                        <div class="flex gap-3">
                            <x-admin.form.new-input containerClass="flex-1"
                                                    type="text"
                                                    name="{{ 'name[' . $locale . ']' }}"
                                                    id="attribute_name_{{ $locale }}" type="text"
                                                    :required="$locale == 'pl'"
                                                    value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                    min="3" max="50" />
                            <select name="icon" class="icon-picker">
                                @foreach ($images as $image)
                                    <option value="{{ $image['path'] }}"
                                            title="{{ $image['name'] }}" {{ $image['path']===$data->icon ? 'selected' : ''}}>{{ $image['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-admin.form.label for="attribute_description_{{ $locale }}"
                                            value="{{ __('admin.dishes.description') }}" />
                        <x-admin.form.new-input type="textarea"
                                                name="{{ 'description[' . $locale . ']' }}"
                                                id="attribute_description_{{ $locale }}"
                                                value="{{ old('description.' . $locale, $data->getTranslation('description', $locale)) }}"
                                                min="3" max="1000" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
        <div>
            <x-admin.form.label value="{{__('admin.attribute_group')}}" for="attribute_group" :required="true" />
            <x-admin.form.new-input type="select" name="attribute_group_id" id="attribute_group" mode="single"
                                    :value="route('admin.attributes.attribute_group',['id' => $data->id])"
                                    :oldValue="$oldAttributeGroup" />
        </div>
        <div class="flex flex-col 2xl:flex-row gap-6 justify-center">
            <x-admin.form.new-input type="toggle" name="is_active" id="is_active"
                                    checked="{{ old('is_active', $data->is_active) }}"
                                    :placeholder="__('attributes.is_attribute_active')" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
