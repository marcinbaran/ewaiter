@props(['multiple' => false, 'namespace' => '', 'files' => [], 'disabled' => false, 'name' => '', 'accept' => '*', 'id' => 0, 'additionalData' => [], 'required' => false, 'aspectRatio' => 'square'])

@if(!$id || $id == 0)
    @php
        $id = old('temp_gallery_id', \Illuminate\Support\Str::password(10, true, false, false));
    @endphp
@endif
<div>
    <x-admin.form.warning class="mb-2">{{ __('admin.edit-file-after-upload') }}</x-admin.form.warning>
    <input type="hidden" class="ring-2 ring-red-600" name="temp_gallery_id" value="{{ $id }}" />
    <input
        type="file"
        class="fileupload"
        {{ $disabled ? 'disabled' : '' }}
        data-name="{{ $name }}"
        data-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-required="{{ $required ? 'true' : 'false'}}"
        data-label="{{ __('admin.browse files') }}"
        data-allow-reorder="true"
        data-processurl="{{ route('admin.upload.process', ['id' => $id, 'namespace' => $namespace]) }}"
        data-loadurl="{{ route('admin.upload.load', ['namespace' => $namespace]) }}"
        data-reverturl="{{ route('admin.upload.revert', ['namespace' => $namespace]) }}"
        data-additionaldata="{{ json_encode($additionalData) }}"
        data-photos="{{ $files }}"
        data-accept="{{ $accept }}"
        data-aspectratio="{{ $aspectRatio }}"
    >
    <p class="error-message text-red-600"></p>
</div>

