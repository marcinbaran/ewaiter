<x-admin.form.gallery
    :files="$data->getJsonFile($key)"
    name="{{ $key }}"
    :additional-data="['file_type' => $key]"
    accept="image/png, image/jpeg, image/jpg, image/webp"
    namespace="settings"
    :required="true"
    :id="$data->id"
/>
<input type="hidden" name="value[{{ $key }}]" value="file" />
