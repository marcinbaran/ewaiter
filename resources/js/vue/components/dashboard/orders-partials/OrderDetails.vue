<script setup>
import { computed, defineProps } from "vue";

import DeliveryInfo from "./DeliveryInfo.vue";
import HorizontalDivider from "./HorizontalDivider.vue";
import Dish from "./Dish.vue";
import Bundle from "./Bundle.vue";

const props = defineProps({
    orders: {
        type: Array,
        required: true
    },
    class: {
        type: String,
        default: ""
    },
    deliveryType: {
        type: String,
        required: true
    },
    deliveryRoomNumber: {
        type: String
    },
    deliveryTableNumber: {
        type: String,
        default: null
    },
    paymentType: {
        type: String,
        required: true
    },
    isScrollable: {
        type: Boolean,
        default: true
    }
});

const parentClass = computed(() => {
    return `min-h-full flex flex-col gap-2 justify-between ${
        props.class ?? ""
    }`;
});

const parentElement = computed(() => {
    return props.isScrollable ? "perfect-scrollbar" : "div";
});
</script>
<template>
    <component :is="parentElement">
        <div :class="parentClass">
            <h3 class="font-bold">{{ $t("orders") }}</h3>
            <ol class="list-decimal flex-grow text-sm pl-4">
                <li v-for="order in orders" :key="order.id" class="pb-2">
                    <dish v-if="order.bundle === undefined" :additions="order.additions" :name="order.dish.name"
                          :price="order.price"
                          :quantity="order.quantity" />
                    <bundle v-else :additions="order.additions" :bundle="order.bundle" :price="order.price"
                            :quantity="order.quantity" />
                </li>
            </ol>
            <HorizontalDivider />
            <DeliveryInfo
                :delivery-room-number="deliveryRoomNumber"
                :delivery-table-number="deliveryTableNumber"
                :delivery-type="deliveryType"
                :payment-type="paymentType"
                class="text-sm"
            />
        </div>
    </component>
</template>
