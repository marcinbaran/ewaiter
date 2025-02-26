<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.labels.update', ['id' => $data->id]) : route('admin.labels.store');
    @endphp
    <x-admin.form.form
        role="form"
        method="POST"
        id="labelsForm"
        enctype="multipart/form-data"
        :action="$action"
        :redirectUrl="$redirectUrl"
    >
        <x-admin.form.tablist id="myTabContent" :data="$data">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-t-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div class="group relative mb-6 w-full">
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" for="label_name_{{ $locale }}"
                                            value="{{ __('admin.Label') }}" :required="$locale == 'pl'" />
                        <div class="flex gap-3">
                            <x-admin.form.new-input id="label_name_{{ $locale }}"
                                                    name="{{ 'name[' . $locale . ']' }}"
                                                    type="text"
                                                    value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                    containerClass="flex-1" min="3" max="100"
                                                    :required="$locale == 'pl'" />
                            <div class="h-full aspect-square">
                                <select name="icon" class="icon-picker">
                                    @foreach ($images as $image)
                                        <option value="{{ $image['path'] }}"
                                                title="{{ $image['name'] }}" {{ $image['path']===$data->icon ? 'selected' : ''}}>{{ $image['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
    </x-admin.form.form>

</x-admin.layout.admin-layout>
