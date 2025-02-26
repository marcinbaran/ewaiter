@props(['data' ,'user_roles','options'])

<div class="setting flex flex-col gap-2 py-4">
    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('settings.value.logo') }}</p>
    <x-admin.form.gallery
        :files="$data->getJsonFile('logo')"
        name="logo"
        :additional-data="['file_type' => 'logo']"
        accept="image/png, image/jpeg, image/jpg, image/webp"
        namespace="settings"
        :required="true"
        requiredText="{{ __('validation.image_required') }}"
        requiredSingleText="{{ __('validation.single_image_required') }}"
        :id="$data->id"
    />
</div>
<div class="setting flex flex-col gap-2 py-4">
    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('settings.value.bg_image') }}</p>
    <x-admin.form.gallery
        :files="$data->getJsonFile('bg_image')"
        :additional-data="['file_type' => 'bg_image']"
        name="bg_image"
        accept="image/png, image/jpeg, image/jpg, image/webp"
        namespace="settings"
        :required="true"
        requiredText="{{ __('validation.image_required') }}"
        requiredSingleText="{{ __('validation.single_image_required') }}"
        :id="$data->id"
        aspect-ratio="rectangle"
    />
</div>
@foreach ($options as $option)
    @if(!empty(array_intersect($user_roles, $option['roles'])))
        @if($option['isWebsite'] == $isWebsite)
            <x-admin.settings.forms.logo-item :data="$data" />
        @endif
    @endif
@endforeach
