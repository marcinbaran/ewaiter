@props(['data'])

@php
    $isToggleChecked = $data->resource->value_active['czas'];
    $key = array_keys($data->resource->value)[0];
@endphp

<div class="setting flex w-full flex-col gap-2 py-4">
    <input type="hidden" name="value[{{$key}}]" value="0" id="delivery_disable_time_input">
    <x-admin.form.new-input type="toggle" name="value_active[{{$key}}]" id="{{ $data->key }}"
                            checked="{{ $isToggleChecked }}"
                            placeholder="{{ __('settings.value.czas')}}" />
    <div class="w-full flex">
        <x-admin.form.new-input type="select" class="w-full" id="delivery_disable_time" container-class="flex-1"
                                name="select2" required="true">
            @for ($i = 0; $i <= config('delivery_disable_time.max_disable_time'); $i += config('delivery_disable_time.step'))
                <option value="{{ $i }}">{{floor($i / 60) }}h {{ $i % 60 }}m</option>
            @endfor
        </x-admin.form.new-input>
    </div>

</div>
@section('bottomscripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const dataValue = @json($data->value);
            const jsonData = JSON.parse(dataValue);

            $("select#delivery_disable_time").on("change", function() {
                const value = this.value;
                if (value >= 0) {
                    $("#delivery_disable_time_input").val(formatSelect2Value(value));
                }
            });

            if (deformatSelect2Value(jsonData.czas) >= 0) {
                $("#delivery_disable_time").val(deformatSelect2Value(jsonData.czas)).trigger("change");
            }
        });

        const formatSelect2Value = (value) => {
            const hours = Math.floor(value / 60);
            const minutes = value % 60;
            return `${hours < 10 ? "0" + hours : hours}:${minutes < 10 ? "0" + minutes : minutes}`;
        };

        const deformatSelect2Value = (value) => {
            const hours = value.split(":")[0];
            const minutes = value.split(":")[1];
            return parseInt(hours) * 60 + parseInt(minutes);
        };
    </script>
@endsection
