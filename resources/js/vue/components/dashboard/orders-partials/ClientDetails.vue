<script setup>
import { computed, defineProps } from "vue";

import PayInfo from "./PayInfo.vue";
import HorizontalDivider from "./HorizontalDivider.vue";

const props = defineProps({
    class: {
        type: String,
        default: "",
    },
    companyName: {
        type: String,
        default: "",
    },
    firstName: {
        type: String,
        default: "",
    },
    email: {
        type: String,
        default: "",
    },
    phone: {
        type: String,
        default: "",
    },
    street: {
        type: String,
        default: "",
    },
    buildingNumber: {
        type: String,
        default: "",
    },
    flatNumber: {
        type: String,
        default: "",
    },
    postcode: {
        type: String,
        default: "",
    },
    city: {
        type: String,
        default: "",
    },
    comment: {
        type: String,
        default: "",
    },
    isOrderPaid: {
        type: Boolean,
        default: null,
    },
    orderPrice: {
        type: String,
        default: null,
    },
    orderPoints: {
        type: Number,
        default: null,
    },
    isScrollable: {
        type: Boolean,
        default: true,
    },
    showPriceDetailsModalHandler: {
        type: Function,
        default: () => {},
    },
});

const parentClass = computed(() => {
    return `new-order--client-details min-h-full flex flex-col gap-2 ${props.class}`;
});

const isFullAddress = computed(
    () => props.street && props.buildingNumber && props.postcode && props.city,
);

const parentElement = computed(() => {
    return props.isScrollable ? "perfect-scrollbar" : "div";
});
</script>

<template>
    <component :is="parentElement">
        <div :class="parentClass">
            <h3 class="font-bold">{{ $t("client_details") }}</h3>
            <div class="flex-grow flex gap-6 text-sm overflow-hidden">
                <ul class="flex-1 flex flex-col">
                    <li v-if="companyName">{{ companyName }}</li>
                    <li v-if="firstName">{{ firstName }}</li>
                    <li v-if="email">{{ email }}</li>
                    <li v-if="phone">{{ phone }}</li>
                    <li v-if="isFullAddress">
                        <p>
                            {{
                                `${street} ${buildingNumber}${
                                    flatNumber ? "/" + flatNumber : ""
                                }`
                            }}
                        </p>
                        <p>{{ `${postcode} ${city}` }}</p>
                    </li>
                </ul>
                <div class="flex-1 italic text-gray-600 dark:text-gray-400">
                    {{ comment ?? $t("no_comment") }}
                </div>
            </div>
            <HorizontalDivider v-if="orderPrice !== null" />
            <PayInfo
                v-if="orderPrice !== null"
                :isPaid="isOrderPaid"
                :price="orderPrice"
                :points="orderPoints"
                :show-price-details-modal-handler="showPriceDetailsModalHandler"
                class="text-sm"
            />
        </div>
    </component>
</template>
