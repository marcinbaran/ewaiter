<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.delivery_ranges.create', ['delivery_range' => $data->id]) : route('admin.delivery_ranges.store');
    @endphp

    <x-admin.form.form id="delivery_range" role="form" method="POST" :action="$action" enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl">
        <p class="text-center text-2xl dark:text-white">{{ __('admin.Delivery range') }}</p>
        <div class="mb-6 flex flex-col ">
            <x-admin.form.label for="delivery_range_delivery_range" value="{{ __('delivery_ranges.Name') }}" />
            <x-admin.form.input class="form-control rounded-lg" id="delivery_range_name" name="name" type="text"
                                :value="$data->name" minlength="3" maxlength="50" />
        </div>
        <div class="mb-6 flex flex-col hidden">
            <x-admin.form.toggle id="delivery_range_out_of_range" name="out_of_range" name="out_of_range"
                                 :checked="$data->out_of_range">
                {{ __('delivery_ranges.Out of range') }}
            </x-admin.form.toggle>
        </div>
        {{--        <div class="mb-6 flex flex-col">--}}
        {{--            <x-admin.form.label for="delivery_range_delivery_range" value="{{ __('delivery_ranges.Range polygon') }}" />--}}
        {{--            <x-admin.form.input class="form-control rounded-lg" id="delivery_range_range_polygon" name="range_polygon"--}}
        {{--                                type="text"--}}
        {{--                                :value="$data->range_polygon" />--}}
        {{--        </div>--}}
        <div id="polygon-delivery-range" class="mb-6 flex flex-col">
            <x-admin.form.label for="range_polygon" value="{{ __('delivery_ranges.Range Polygon') }}"
                                :required="true" />
            <polygon-delivery-range-configuration />
        </div>
        <div class="mb-6 flex flex-col">
            <x-admin.form.label for="delivery_range_min_value" value="{{ __('delivery_ranges.Minimum value') }}" />
            <x-admin.form.input-group class="input-mask" id="delivery_range_min_value" name="min_value" format="money"
                                      type="text" value="{{ old('min_value', $data->min_value) }}" prepend="PLN" />
        </div>
        <div class="mb-6 flex flex-col">
            <x-admin.form.label for="delivery_range_free_from" value="{{ __('delivery_ranges.Free from') }}" />
            <x-admin.form.input-group class="input-mask" id="delivery_range_free_from" name="free_from" format="money"
                                      type="text" value="{{ old('free_from', $data->free_from) }}" prepend="PLN" />
        </div>

        {{--        <div class="mb-6 flex flex-col">--}}
        {{--            <x-admin.form.label for="delivery_costs" value="{{ __('delivery_ranges.Delivery cost type') }}"--}}
        {{--                                :required="true" />--}}
        {{--            <x-admin.form.select id="delivery_costs" name="delivery_costs">--}}
        {{--                <option value="delivery_range_cost"--}}
        {{--                    {{ old('delivery_costs') == 'delivery_range_cost' || $data->cost > 0 ? 'selected' : '' }}>--}}
        {{--                    {{ __('delivery_ranges.Delivery cost') }}</option>--}}
        {{--                <option value="delivery_range_km_cost"--}}
        {{--                    {{ old('delivery_costs') == 'delivery_range_km_cost' || $data->km_cost > 0 ? 'selected' : '' }}>--}}
        {{--                    {{ __('delivery_ranges.Delivery km cost') }}</option>--}}
        {{--            </x-admin.form.select>--}}
        {{--        </div>--}}
        <div class="mb-6 flex-col" id="div_delivery_range_cost">
            <x-admin.form.label for="delivery_range_cost" value="{{ __('delivery_ranges.Delivery cost') }}"
                                :required="true" />
            <x-admin.form.input-group class="input-mask" id="delivery_range_cost" name="cost" format="money"
                                      type="text" value="{{ old('cost', $data->cost) }}" prepend="PLN"
                                      :required="true" />
        </div>
        {{--        <div class="mb-6 hidden flex-col" id="div_delivery_range_km_cost">--}}
        {{--            <x-admin.form.label for="delivery_range_km_cost" value="{{ __('delivery_ranges.Delivery km cost') }}"--}}
        {{--                                :required="true" />--}}
        {{--            <x-admin.form.input-group class="input-mask" id="delivery_range_km_cost" name="km_cost" format="money"--}}
        {{--                                      type="text" value="{{ old('km_cost', $data->km_cost) }}" prepend="PLN"--}}
        {{--                                      :required="true" />--}}
        {{--        </div>--}}
    </x-admin.form.form>

    @section('bottomscripts')
        <script type="text/javascript">
            const select = document.getElementById("delivery_costs");
            const deliveryCost = {
                div: document.getElementById("div_delivery_range_cost"),
                input: document.getElementById("delivery_range_cost")
            };
            const deliveryKmCost = {
                div: document.getElementById("div_delivery_range_km_cost"),
                input: document.getElementById("delivery_range_km_cost")
            };
            const checkDelivery = () => {
                const option = select.options[select.selectedIndex];
                const value = option.value;
                if (value === "delivery_range_cost") {
                    deliveryCost.div.classList.remove("hidden");
                    deliveryCost.div.classList.add("flex");
                    deliveryCost.input.disabled = false;
                    deliveryCost.input.required = true;
                    deliveryKmCost.div.classList.remove("flex");
                    deliveryKmCost.div.classList.add("hidden");
                    deliveryKmCost.input.disabled = true;
                    deliveryKmCost.input.required = false;
                } else if (value === "delivery_range_km_cost") {
                    deliveryKmCost.div.classList.remove("hidden");
                    deliveryKmCost.div.classList.add("flex");
                    deliveryKmCost.input.disabled = false;
                    deliveryKmCost.input.required = true;
                    deliveryCost.div.classList.remove("flex");
                    deliveryCost.div.classList.add("hidden");
                    deliveryCost.input.disabled = true;
                    deliveryCost.input.required = false;
                }
            };
            document.addEventListener("DOMContentLoaded", () => {
                checkDelivery();
                select.addEventListener("change", checkDelivery);
            });
        </script>
    @append
</x-admin.layout.admin-layout>

