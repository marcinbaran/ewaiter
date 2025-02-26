@props(['data'])

@php
    $isToggleChecked = $data->resource->value_active['service_charge'] || $data->resource->value_active['service_charge_procent'];
    $activeValue = $data->resource->value_active['service_charge'] ? 'service_charge' : 'service_charge_procent';
@endphp

<div class="setting flex flex-col gap-2 py-4">
    <input type="hidden" name="value" value="0">
    <input type="hidden" name="percentage_value" value="{{$data->resource->value['service_charge_procent']}}">
    <input type="hidden" name="money_value" value="{{$data->resource->value['service_charge']}}">
    <div class="setting flex flex-col gap-2 py-4">
        <x-admin.form.new-input type="toggle" name="value_active" id="{{ $data->key }}"
                                checked="{{ $isToggleChecked }}"
                                placeholder="{{ __('admin.service_charge')}}" />
        <div class="w-full flex">
            <x-admin.form.new-input type="percent"
                                    container-class="flex-1"
                                    id="service_charge_percentage_value"
                                    name="percentage_value" max="99"
                                    value="{{ $data->resource->value['service_charge_procent'] }}" />
            <x-admin.form.new-input type="money"
                                    container-class="flex-1"
                                    id="service_charge_money_value"
                                    name="money_value"
                                    value="{{ $data->resource->value['service_charge'] }}" />
            <x-admin.form.select class="!w-auto rounded-l-none" id="service_charge_value_type" name="typeValue"
                                 :required="true">
                <option
                    value="procent" {{ $activeValue === 'service_charge_procent' ? 'selected' : '' }}>
                    %
                </option>
                <option
                    value="money" {{ $activeValue === 'service_charge' ? 'selected' : '' }}>
                    PLN
                </option>
            </x-admin.form.select>
        </div>
    </div>
</div>
@section('bottomscripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const removeRightBorderRadiusFromNewInputs = (...inputs) => {
                inputs.forEach((input) => input.container.classList.add("rounded-r-none"));
            };

            const changeNewInputVisibility = (newInput, isVisible) => {
                newInput.input.disabled = !isVisible;
                newInput.input.required = isVisible;
                newInput.parentContainer.parentElement.classList.toggle("hidden", !isVisible);
            };


            const select = document.querySelector("select#service_charge_value_type");
            const toggle = document.querySelector("input#service_charge");

            const percent = newInputInstances.find((instance) => instance.input.id === "service_charge_percentage_value");
            const money = newInputInstances.find((instance) => instance.input.id === "service_charge_money_value");

            const switchInputs = (isPercentage) => {
                if (isPercentage) {
                    changeNewInputVisibility(percent, true);
                    changeNewInputVisibility(money, false);
                } else {
                    changeNewInputVisibility(percent, false);
                    changeNewInputVisibility(money, true);
                }
            };

            toggle.addEventListener("change", (e) => {
                select.disabled = !e.target.checked;
                percent.changeInputDisabilityState(!e.target.checked);
                money.changeInputDisabilityState(!e.target.checked);
            });

            select.addEventListener("change", (e) => {
                switchInputs(e.target.value === "procent");
            });

            switchInputs(select.value === "procent");
            select.disabled = !toggle.checked;
            changeNewInputVisibility(percent, select.value === "procent");
            changeNewInputVisibility(money, select.value === "money");
            removeRightBorderRadiusFromNewInputs(percent, money);
            percent.changeInputDisabilityState(!toggle.checked);
            money.changeInputDisabilityState(!toggle.checked);
        });
    </script>
@endsection

