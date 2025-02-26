<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.tags.update', ['tag' => $data->id]) : route('admin.tags.store');
    @endphp
    <x-admin.form.form id="tags" role="form" method="POST" :redirectUrl="$redirectUrl" :action="$action"
                       enctype="multipart/form-data" formWide="w-1/2" class="flex flex-col gap-6">
        <div>
            <x-admin.form.new-input type="toggle" id="tag_visibility" name="visibility"
                                    :checked="old('visibility', $data->visibility)"
                                    placeholder="{{ __('admin.Active') }}" />
        </div>
        <x-admin.form.tablist id="myTabContent" :data="$data">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" for="tag_name_{{ $locale }}"
                                            value="{{ __('admin.Name') }}" :required="$locale == 'pl'"
                                            for="tag_name_{{ $locale }}" />
                        <div class="flex w-full justify-between gap-6">
                            <x-admin.form.new-input id="tag_name_{{ $locale }}"
                                                    name="{{ 'name[' . $locale . ']'}}"
                                                    type="text"
                                                    id="tag_name_{{ $locale }}"
                                                    value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                    containerClass="flex-1" min="3" max="100"
                                                    :required="$locale == 'pl'" />
                            <div class="relative h-full">
                                <livewire:icon-picker :initialIcon="$data->icon" />
                            </div>
                        </div>
                    </div>
                    @if (!empty(array_intersect(config('options_visibility.tagController')[0]['roles'], $user_roles)))
                        <div>
                            <x-admin.form.label for="tag_description_{{ $locale }}"
                                                value="{{ __('admin.Description') }}"
                                                for="tag_description_{{ $locale }}" />
                            <x-admin.form.new-input type="textarea" id="tag_description_{{ $locale }}"
                                                    name="{{ 'description[' . $locale . ']'}}"
                                                    id="tag_description_{{ $locale }}"
                                                    min="3" max="1000"
                                                    value="{{ old('description.' . $locale, $data->getTranslation('description', $locale)) }}" />
                        </div>
                    @endif
                </div>
            @endforeach
        </x-admin.form.tablist>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
