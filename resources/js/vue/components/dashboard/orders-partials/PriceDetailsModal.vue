<template>
    <div
        class="absolute inset-0 flex flex-col z-30 p-3 text-gray-900 bg-gray-200/95 dark:text-gray-50 dark:bg-gray-800/95 backdrop-blur"
    >
        <div class="flex pl-4 justify-between">
            <h2 class="text-xl font-bold">{{ $t("order_summary") }}</h2>
            <NavigationButton :click-handler="onClose">
                <IconX size="1.5rem" />
            </NavigationButton>
        </div>
        <HorizontalDivider />
        <perfect-scrollbar class="flex-1">
            <div class="flex flex-col gap-2 px-4 py-2">
                <div class="flex justify-between font-light">
                    <span>{{ $t("dish_price") }}</span>
                    <span>{{ props.dishPrice }}</span>
                </div>
                <div class="flex justify-between font-light">
                    <span>{{ $t("delivery_price") }}</span>
                    <span>{{ props.deliveryPrice }}</span>
                </div>
                <div class="flex justify-between font-light">
                    <span>{{ $t("package_price") }}</span>
                    <span>{{ props.packagePrice }}</span>
                </div>
                <div class="flex justify-between font-light">
                    <span>{{ $t("service_charge") }}</span>
                    <span>{{ props.serviceCharge }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>{{ $t("total_price") }}</span>
                    <span>{{ totalPrice }}</span>
                </div>
                <HorizontalDivider />
                <div
                    class="flex justify-between font-light text-primary-900 dark:text-lime-400"
                >
                    <span>{{ $t("discount") }}</span>
                    <span>-{{ props.discountValue }}</span>
                </div>
                <div
                    class="flex justify-between font-light text-primary-900 dark:text-lime-400"
                >
                    <span>{{ $t("points_value") }}</span>
                    <span>-{{ props.pointsValue }}</span>
                </div>
                <div
                    class="flex justify-between font-bold"
                    :class="{
                        'text-green-500': isPaid,
                        'text-red-500': !isPaid,
                    }"
                >
                    <span class="flex gap-1"
                        >{{ $t("price_to_pay") }}
                        <IconCheck
                            v-if="isPaid"
                            size="1.5rem"
                            class="stroke-green-500"
                        />
                    </span>
                    <span>{{ priceToPay }}</span>
                </div>
            </div>
        </perfect-scrollbar>
    </div>
</template>

<script setup lang="ts">
import { defineProps, computed } from "vue";
import { IconX, IconCheck } from "@tabler/icons-vue";

import NavigationButton from "../hero-widget-partials/navigation-partials/NavigationButton.vue";
import HorizontalDivider from "./HorizontalDivider.vue";

const props = defineProps({
    dishPrice: {
        type: String,
        required: true,
    },
    deliveryPrice: {
        type: String,
        required: true,
    },
    packagePrice: {
        type: String,
        required: true,
    },
    serviceCharge: {
        type: String,
        required: true,
    },
    discountValue: {
        type: String,
        required: true,
    },
    pointsValue: {
        type: String,
        required: true,
    },
    onClose: {
        type: Function,
        required: true,
    },
    isPaid: {
        type: Boolean,
        required: true,
    },
});

const totalPrice = computed((): string => {
    const price = (
        parseFloat(props.dishPrice) +
        parseFloat(props.deliveryPrice) +
        parseFloat(props.packagePrice) +
        parseFloat(props.serviceCharge)
    ).toFixed(2);

    return price;
});

const priceToPay = computed((): string => {
    const price = (
        parseFloat(totalPrice.value) -
        parseFloat(props.discountValue) -
        parseFloat(props.pointsValue)
    ).toFixed(2);

    return price;
});
</script>

<style scoped></style>
