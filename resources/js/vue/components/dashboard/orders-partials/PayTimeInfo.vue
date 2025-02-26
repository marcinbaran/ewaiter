<script setup>
import { defineProps, watch, computed } from "vue";
import { IconExclamationCircle, IconEdit } from "@tabler/icons-vue";

const props = defineProps({
    isPaid: {
        type: Boolean,
        required: true,
    },
    price: {
        type: String,
        required: true,
    },
    points: {
        type: Number,
        default: 0,
    },
    waitTime: {
        type: String,
        required: true,
    },
    class: {
        type: String,
        default: "",
    },
    showPriceDetailsModalHandler: {
        type: Function,
        default: () => {},
    },
    showWaitTimeUpdateModal: {
        type: Function,
        default: () => {},
    },
});

const openEditWaitTimeModalHandler = (event) => {
    event?.stopPropagation();
    props.showWaitTimeUpdateModal();
};

const formattedWaitTime = computed(() => props.waitTime.replace("T", " "));

const mergedClasses = computed(
    () =>
        `flex gap-3 justify-between text-sm hover:contrast-200 cursor-pointer ${props.class}`,
);

const pointsPrice = computed(() => (props.points / 100).toFixed(2));
</script>
<template>
    <ul :class="mergedClasses" @click="showPriceDetailsModalHandler">
        <li class="flex gap-2 items-center">
            <span>{{ $t("to_pay") }}:</span>
            <span
                class="flex gap-1 items-center"
                :class="{
                    'text-green-500': isPaid,
                    'text-red-500': !isPaid,
                }"
            >
                {{ `${price} PLN` }}
                <IconExclamationCircle size="1.25rem" v-if="!isPaid" />
            </span>
        </li>
        <li class="flex gap-2">
            <span>{{ $t("points") }}:</span>
            <span class="text-blue-600 dark:text-blue-500">
                {{ points ?? 0 }} ({{ pointsPrice }} PLN)
            </span>
        </li>
        <li
            @click="openEditWaitTimeModalHandler"
            class="flex items-center gap-1"
        >
            <span>{{ $t("wait_time") }}:</span>
            <span class="text-blue-600 dark:text-blue-500">
                {{ formattedWaitTime }}
            </span>
            <IconEdit class="w-4 h-4 mr-2 stroke-blue-600" />
        </li>
    </ul>
</template>
