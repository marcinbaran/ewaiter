<script setup>
import { format, addMinutes } from "date-fns";
import config from "../../../../../config/config";

import { ref, computed } from "vue";
import NavigationButton from "../hero-widget-partials/navigation-partials/NavigationButton.vue";
import {IconChevronDown, IconChevronUp, IconX} from "@tabler/icons-vue";
import HorizontalDivider from "./HorizontalDivider.vue";
import Button from "./Button.vue";

const props = defineProps({
    currentWaitTime: {
        type: String,
        required: true,
    },
    isUpdatingWaitTime: {
        type: Boolean,
        required: true,
    },
    hasError: {
        type: Boolean,
        required: true,
    },
    closeWaitTimeUpdateModal: {
        type: Function,
        required: true,
    },
    updateWaitTime: {
        type: Function,
        required: true,
    },
});
const waitTime = ref(0);
const UPDATE_TIME = 15;

const isAcceptButtonBlocked = computed(() => waitTime.value === 0);
const formattedWaitTime = computed(() => format(new Date(props.currentWaitTime), config.DATE_TIME_FORMAT_FNS));

const formattedWaitTimeHours = computed(() => {
    const date = new Date(formattedWaitTime.value);
    return format(date, "HH:mm");
});

const updateWaitTime = computed(() => {
    const date = new Date(formattedWaitTime.value);
    return format(addMinutes(date, waitTime.value), "HH:mm");
});

const isDownArrowBlocked = computed(() => waitTime.value === 0);

const increaseInputValue = () => {
    waitTime.value += UPDATE_TIME;
};

const decreaseInputValue = () => {
    if (waitTime.value > 0) {
        waitTime.value -= UPDATE_TIME;
    }
};

const closeWaitTimeUpdateModalHandler = () => {
    props.closeWaitTimeUpdateModal();
};

const updateWaitTimeHandler = () => {
    props.updateWaitTime(waitTime.value);
};
</script>

<template>
    <div class="absolute inset-0 flex flex-col z-30 p-3 text-gray-900 bg-gray-200/95 dark:text-gray-50 dark:bg-gray-800/95 backdrop-blur">
        <div class="flex pl-4 justify-between">
            <h2 class="text-xl font-bold">{{ $t("update_time_delivery") }}</h2>
            <NavigationButton :click-handler="closeWaitTimeUpdateModalHandler">
                <IconX size="1.5rem" />
            </NavigationButton>
        </div>
        <HorizontalDivider />
        <div class="fade-in flex h-full inset-0 items-center justify-center">
            <div class="flex flex-col justify-center items-center">
                <button @click="increaseInputValue" id="increase">
                    <IconChevronUp size="3rem" />
                </button>
                <div class="input relative flex justify-center">
                    <input
                        type="text"
                        :value="waitTime"
                        class="w-1/2 p-2.5 text-lg text-gray-900 dark:text-gray-50 bg-gray-100 dark:bg-gray-500 border-gray-300 dark:border-gray-700 rounded-md text-center"
                    />
                    <div class="absolute inset-0"></div>
                    </div>
                        <button
                        @click="decreaseInputValue"
                        id="decrease"
                        :disabled="isDownArrowBlocked"
                        class="disabled:opacity-25 disabled:cursor-not-allowed"
                    >
                        <IconChevronDown size="3rem" />
                    </button>
                </div>
                <div class="flex flex-col gap-2">
                    <ul>
                        <li>
                            <span>{{$t('actual_delivery_time')}}: {{formattedWaitTimeHours}}</span>
                        </li>
                        <li>
                            <span>{{$t('updated_delivery_time')}}: {{ updateWaitTime }}</span>
                        </li>
                        <li>
                            <span v-if="hasError" class="text-red-500">{{$t('something_went_wrong')}}</span>
                        </li>
                    </ul>
                    <div v-if="!isUpdatingWaitTime" class="flex gap-2">
                        <Button :on-click="closeWaitTimeUpdateModalHandler" color="danger">
                            {{$t('cancel')}}
                        </Button>
                        <Button :on-click="updateWaitTimeHandler"  :disabled="isAcceptButtonBlocked" color="success">
                            {{$t('update')}}
                        </Button>
                    </div>
                    <div v-else>
                        <div class="flex justify-center items-center gap-2">
                            <div class="loader "></div>
                            <span>{{$t('updating')}}</span>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</template>

<style scoped>
.fade-in {
    animation: fadeIn 0.5s;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
