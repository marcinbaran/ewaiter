<script setup>
import { computed, defineProps, onMounted, ref, watch } from "vue";
import { addMinutes, format } from "date-fns";
import { IconChevronDown, IconChevronUp } from "@tabler/icons-vue";
import { debounce, sendRequest } from "../../../../helpers";

const props = defineProps({
    updateWaitTime: Function,
    order: Object,
    showError: Boolean,
    isNewOrder: Boolean,
    isPageChanging: Boolean
});

const WAIT_TIME_CHANGE_VALUE = 15;
const hoursWaitTime = ref(null);
const currentDateTime = ref(null);
const inputRef = ref(null);
const waitTime = ref(null);

const isDownArrowBlocked = computed(() => {
    return waitTime.value <= 0 || waitTime.value == null;
});

const updateCurrentTimeFromServer = debounce(() => {
    sendRequest("/admin/dashboard/server-time")
        .then((response) => {
            currentDateTime.value = new Date(
                `${response.current_date} ${response.current_time}`
            );
        })
        .catch((error) => {
            console.log(error);
        });
}, 1000);

watch(
    () => waitTime.value,
    () => {
        if (props.isNewOrder) {
            updateCurrentTimeFromServer();
            if (currentDateTime.value) {
                hoursWaitTime.value = format(
                    addMinutes(currentDateTime.value, waitTime.value),
                    "HH:mm"
                );
            }
            props.updateWaitTime(parseInt(waitTime.value));
        }
    }
);

watch(
    () => props.order,
    () => {
        if (props.isPageChanging) {
            waitTime.value = 0;
        }
    }
);

onMounted(() => {
    if (props.isNewOrder) {
        inputRef.value.value = 0;
        waitTime.value = parseInt(inputRef.value.value);
    }
});

const increaseInputValueByFive = () => {
    inputRef.value.value =
        parseInt(inputRef.value.value == "" ? 0 : inputRef.value.value) +
        WAIT_TIME_CHANGE_VALUE;
    waitTime.value = parseInt(inputRef.value.value);
};

const decreaseInputValueByFive = () => {
    inputRef.value.value =
        parseInt(inputRef.value.value == "" ? 0 : inputRef.value.value) -
        WAIT_TIME_CHANGE_VALUE <=
        0
            ? 0
            : parseInt(inputRef.value.value) - WAIT_TIME_CHANGE_VALUE;
    waitTime.value = parseInt(inputRef.value.value);
};

onMounted(() => {
    sendRequest("/admin/dashboard/server-time")
        .then((response) => {
            currentDateTime.value = new Date(
                `${response.current_date} ${response.current_time}`
            );
        })
        .catch((error) => {
            console.log(error);
        });
});
</script>

<template>
    <ul class="flex flex-col gap-2 text-sm leading-5">
        <li v-if="isNewOrder" class="flex items-center justify-between gap-2">
            <div class="flex flex-grow flex-col justify-center gap-2">
                <span class="block"
                >{{ $t("wait_time") }} ({{ $t("min") }}):
                </span>
                <span class="block text-xs">
                    {{ $t("delivery_time") }}
                    {{ hoursWaitTime }}
                </span>
            </div>
            <div class="flex-1 flex justify-end gap-3">
                <button id="increase" @click="increaseInputValueByFive">
                    <IconChevronUp size="2rem" />
                </button>
                <div class="input relative w-20">
                    <input
                        ref="inputRef"
                        :class="{
                            'border-red-600': showError,
                        }"
                        :value="waitTime"
                        class="w-full p-2.5 text-gray-900 dark:text-gray-50 bg-gray-100 dark:bg-gray-500 border-gray-300 dark:border-gray-700 rounded-md text-center"
                        format="integer"
                        type="text"
                        @input="waitTime = $event.target.value"
                    />
                    <div class="absolute inset-0"></div>
                </div>
                <button
                    id="decrease"
                    :disabled="isDownArrowBlocked"
                    class="disabled:opacity-25 disabled:cursor-not-allowed"
                    @click="decreaseInputValueByFive"
                >
                    <IconChevronDown size="2rem" />
                </button>
            </div>
        </li>
        <li v-if="!isNewOrder">
            <span class="block"
            >{{ $t("wait_time") }}: {{ order.time_wait }}</span
            >
        </li>
        <li v-if="showError" class="text-red-600">
            {{ $t("delivery_time_is_required") }}
        </li>
    </ul>
</template>
