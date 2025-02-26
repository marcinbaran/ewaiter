<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.additions.update', ['addition' => $data->id]) : route('admin.additions.store');
    @endphp

    <x-admin.form.form
        role="form"
        id="additions"
        method="POST"
        :action="$action"
        enctype="multipart/form-data"
        :redirectUrl="$redirectUrl"
        formWide="w-1/2"
        class="flex flex-col gap-6"
    >
        @csrf
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" value="{{ __('admin.Name') }}"
                                            for="addition_name_{{ $locale }}" :required="$locale == 'pl'" />
                        <x-admin.form.new-input type="text"
                                                name="{{'name[' . $locale . ']' }}"
                                                id="addition_name_{{ $locale }}" type="text" :required="$locale == 'pl'"
                                                value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                min="3" max="50" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
        <div>
            <x-admin.form.label value="{{ __('admin.Price') }}" for="addition_price" />
            <x-admin.form.new-input type="money" name="price"
                                    id="addition_price" required="true" value="{{ old('price', $data->price) }}" />
        </div>
        <div>
            <x-admin.form.label value="{{__('admin.Additions groups')}}" for="additions_additions_groups" />
            <x-admin.form.new-input type="select" name="addition_addition_group[][id]" id="additions_additions_groups"
                                    mode="multiple" :oldValue="$oldAdditionGroups"
                                    :value="route('admin.additions.addition_groups',['id' => $data->id])" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
