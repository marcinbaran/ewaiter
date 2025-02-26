@props(['data'])
<div class="pt-4">

    <div class="mb-4">
        <x-admin.form.label for="comment" value="{{ __('online_payment_provider_account.form.comment') }}"
                            :required="false" />
        <x-admin.form.new-input type="text" id="comment" name="comment" :required="false" min="3" max="100"
                                value="{{ old('comment', $data->resource->value['comment']) }}"
                                oninput="checkLength('comment', 'commentLabel', 100)" />
        <label id="commentLabel" style="display:none; color: red;">Osiągnięto limit 100 znaków!</label>
    </div>
    <div class="mb-4">
        <x-admin.form.label for="login" value="{{ __('online_payment_provider_account.form.login') }}"
                            :required="true" />
        <x-admin.form.new-input type="text" name="login" id="login"
                                :value="$data->resource->value['login']"
                                :required="true" />
    </div>
    <div class="mb-4">
        <x-admin.form.label for="password" value="{{ __('online_payment_provider_account.form.password') }}"
                            :required="true" />
        <x-admin.form.new-input type="password" name="password" id="password"
                                :value="$data->resource->value['password']"
                                :min="3" :max="255"
                                :required="true" pattern="^\s*\S{3,253}\s*$"
                                oninput="checkLength('password', 'passwordLabel', 255)" />
        <label id="passwordLabel" style="display:none; color: red;">Osiągnięto limit 255 znaków!</label>
    </div>
    <div class="mb-4">
        <x-admin.form.label for="tpay_api_key" value="{{ __('online_payment_provider_account.form.api_key') }}"
                            :required="true" />
        <x-admin.form.new-input type="text" name="api_key" id="api_key"
                                :value="$data->resource->value['api_key']"
                                :min="3" :max="255"
                                :required="true" pattern="^\s*\S{3,253}\s*$"
                                oninput="checkLength('api_key', 'apiKeyLabel', 255)" />
        <label id="apiKeyLabel" style="display:none; color: red;">Osiągnięto limit 255 znaków!</label>
    </div>
    <div class="mb-4">
        <x-admin.form.label for="tpay_api_password"
                            value="{{ __('online_payment_provider_account.form.api_password') }}"
                            :required="true" />
        <x-admin.form.new-input type="password" name="api_password" id="api_password"
                                :value="$data->resource->value['api_password']"
                                :min="3" :max="255"
                                :required="true" pattern="^\s*\S{3,253}\s*$"
                                oninput="checkLength('api_password', 'apiPasswordLabel', 255)" />
        <label id="apiPasswordLabel" style="display:none; color: red;">Osiągnięto limit znaków!</label>
    </div>
</div>

<script>
    function checkLength(inputId, labelId, maxLength) {
        var inputField = document.getElementById(inputId);
        var label = document.getElementById(labelId);
        if (inputField.value.length >= maxLength) {
            label.style.display = "block";
        } else {
            label.style.display = "none";
        }
    }
</script>
