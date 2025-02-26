<script setup>
import { computed, defineProps, watch } from "vue";
import {
    IconChevronLeft,
    IconChevronRight,
    IconChevronsLeft,
    IconChevronsRight,
} from "@tabler/icons-vue";
import { format } from "date-fns";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
    numberOfPages: {
        type: Number,
        required: true,
    },
    orderId: {
        type: Number,
        required: true,
    },
    orderCreatedAt: {
        type: String,
        required: true,
    },
    changeOrder: Function,
    newOrderShowed: Boolean,
    currentPageNumber: Number,
});

const orderDate = computed(() =>
    format(new Date(props.orderCreatedAt), "dd.MM.yyyy"),
);
const orderTime = computed(() =>
    format(new Date(props.orderCreatedAt), "HH:mm"),
);
const displayPreviusPageNavigation = computed(
    () => props.currentPageNumber > 1,
);
const displayNextPageNavigation = computed(
    () => props.currentPageNumber < props.numberOfPages,
);
const headerText = computed(
    () =>
        `${trans("order")} #${props.orderId} - ${orderDate.value} ${
            orderTime.value
        }`,
);

watch(
    () => props.numberOfPages,
    () => {
        if (props.currentPageNumber > props.numberOfPages) {
            props.changeOrder(props.numberOfPages);
        }
    },
);

const changePage = (page) => {
    props.changeOrder(page);
};
</script>

<template>
    <div
        class="new-order--pagination flex w-full flex-row items-center justify-between border-b border-gray-300 pb-2 dark:border-gray-700"
    >
        <h2 class="flex flex-row items-center gap-2 font-bold">
            <div
                v-if="!newOrderShowed"
                class="new-order-badge animate-pulse flex items-center justify-center text-sm text-gray-50 w-5 h-5 rounded-full bg-red-700"
            ></div>
            <p>{{ headerText }}</p>
        </h2>
        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <IconChevronsLeft
                v-if="displayPreviusPageNavigation"
                @click="changePage(1)"
                class="cursor-pointer"
            />
            <IconChevronLeft
                v-if="displayPreviusPageNavigation"
                @click="changePage(currentPageNumber - 1)"
                class="cursor-pointer"
            />
            <span
                class="w-16 text-center select-none font-bold text-gray-900 dark:text-gray-50"
                >{{ currentPageNumber }} / {{ numberOfPages }}</span
            >
            <IconChevronRight
                v-if="displayNextPageNavigation"
                @click="changePage(currentPageNumber + 1)"
                class="cursor-pointer"
            />
            <IconChevronsRight
                v-if="displayNextPageNavigation"
                @click="changePage(numberOfPages)"
                class="cursor-pointer"
            />
        </div>
    </div>
</template>
