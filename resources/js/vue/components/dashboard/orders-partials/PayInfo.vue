<script setup>
import { defineProps, computed } from "vue";
import { IconExclamationCircle } from "@tabler/icons-vue";

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
});

const mergedClasses = computed(
    () =>
        `flex gap-3 justify-between text-sm hover:contrast-200 cursor-pointer ${props.class}`,
);

const pointsPrice = computed(() => (props.points / 100).toFixed(2));
</script>
<template>
    <ul :class="mergedClasses" @click="showPriceDetailsModalHandler">
        <li class="flex gap-2 items-center">
            <span>{{ $t("to_pay") }}: </span>
            <span
                class="flex gap-1 items-center"
                :class="{
                    'text-green-500': props.isPaid,
                    'text-red-500': !props.isPaid,
                }"
            >
                {{ `${price} PLN` }}
                <IconExclamationCircle size="1.5rem" v-if="!props.isPaid" />
            </span>
        </li>
        <li class="flex gap-2 items-center">
            <span>{{ $t("points") }}:</span>
            <span class="text-blue-600 dark:text-blue-500">
                {{ points ?? 0 }} ({{ pointsPrice }} PLN)
            </span>
        </li>
    </ul>
</template>
