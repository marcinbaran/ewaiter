@props(['data'])

<div class="setting flex flex-col gap-2 py-4">
    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('settings.value.dish_default_image') }}</p>
    <x-admin.form.gallery
        :files="$data->getJsonFile('dish_default_image')"
        :additional-data="['file_type' => 'dish_default_image']"
        name="dish_default_image"
        accept="image/png, image/jpeg, image/jpg, image/webp"
        namespace="settings"
        :required="true"
        requiredText="{{ __('validation.image_required') }}"
        requiredSingleText="{{ __('validation.single_image_required') }}"
        :id="$data->id"
        aspect-ratio="rectangle"
    />
</div>
