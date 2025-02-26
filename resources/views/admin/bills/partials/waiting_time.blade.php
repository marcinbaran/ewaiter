<div class="flex w-full justify-between">
    <div class="bill-time-wait-container flex flex-col items-start gap-3 w-full " data-billid="{{ $data->id }}"
         data-token="{{ csrf_token() }}" data-url="{{ route('admin.bills.time_wait_edit') }}"
         data-toast-success="{{ __('admin.Order was updated') }}"
         data-toast-danger="{{ __('admin.Order update fail') }}"
         data-button-text="{{ __('admin.Send') }}" data-button-sent="{{ __('admin.Has been sent') }}"
         data-button-update="{{ __('admin.Update') }}">
        <div class="placeholder w-10 h-10 flex mx-auto spinner-form">

        </div>
        <div class="input-time-container hidden w-full flex-col items-start gap-3 relative">
            <x-admin.form.new-input type="date-time" id="datetime" containerClass="w-full" :min="\Carbon\Carbon::now()"
                                    value="{{ $data->time_wait }} ?? {{\Carbon\Carbon::now()->addMinutes(15)}}" :step="15" />
            <div class="relative w-full">
                <x-admin.button type="print" id="change_time_btn"
                                class="w-full h-10 text-sm flex justify-center items-center"
                                color="print">
                    {{ __('admin.Send') }}
                    <span class="button-loader"></span>
                </x-admin.button>

                <!-- Tooltip -->
                <div id="pastDateTooltip" role="tooltip" class="absolute hidden bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg shadow-sm tooltip dark:bg-gray-500">
                    {{ __('admin.Cannot set date in the past') }}

                </div>
            </div>
        </div>
        <ul class="w-full display-time-container hidden">
            <x-admin.block.list-element id="canceled" :title="__('admin.Scheduled delivery date')"
                                        value="-" />
            <x-admin.block.list-element id="not-canceled" :title="__('admin.Scheduled delivery date')"
                                        :value="\Carbon\Carbon::parse($data->time_wait)->format('Y-m-d H:i')" />
        </ul>

    </div>

    @section('bottomscripts')
        @parent
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", () => {
                if ('{{ old('refund_amount') }}' != "") {
                    openRefund();
                }
                $.ajaxSetup({
                    beforeSend: function(xhr, settings) {
                        settings.data += "&_token={{ csrf_token() }}";
                    }
                });

                const datetimeInput = document.getElementById('datetime');
                const changeTimeBtn = document.getElementById('change_time_btn');
                const pastDateTooltip = document.getElementById('pastDateTooltip');

                function updateButtonState() {
                    const selectedDate = new Date(datetimeInput.value);
                    const now = new Date();

                    if (selectedDate <= now) {
                        changeTimeBtn.disabled = true;
                        changeTimeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        changeTimeBtn.addEventListener('mouseenter', showTooltip);
                        changeTimeBtn.addEventListener('mouseleave', hideTooltip);
                    } else {
                        changeTimeBtn.disabled = false;
                        changeTimeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        changeTimeBtn.removeEventListener('mouseenter', showTooltip);
                        changeTimeBtn.removeEventListener('mouseleave', hideTooltip);
                        hideTooltip();
                    }
                }

                function showTooltip() {
                    pastDateTooltip.classList.remove('hidden');
                }

                function hideTooltip() {
                    pastDateTooltip.classList.add('hidden');
                }

                // Nasłuchuj na zmiany w inpucie
                datetimeInput.addEventListener('input', updateButtonState);

                // Nasłuchuj również na zdarzenie 'change'
                datetimeInput.addEventListener('change', updateButtonState);

                // Dodatkowo, możemy użyć MutationObserver do śledzenia zmian wartości
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                            updateButtonState();
                        }
                    });
                });

                observer.observe(datetimeInput, { attributes: true });

                // Inicjalne wywołanie
                updateButtonState();
            });

            function openRefund() {
                $("#open_refund_btn").hide();
                $("#bill_refund").show();
            }
        </script>
    @append
</div>
