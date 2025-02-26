<script setup>
import { defineProps, computed } from "vue";
import {
    IconTruck,
    IconWalk,
    IconCoinFilled,
    IconMoped,
    IconCreditCardFilled,
    IconX,
    IconDesk,
    IconDoor,
    IconCreditCard,
    IconCash,
    IconBuilding,
} from "@tabler/icons-vue";

const props = defineProps({
    deliveryType: {
        type: String,
        required: true,
    },
    deliveryRoomNumber: {
        type: String,
        default: "",
    },
    deliveryTableNumber: {
        type: String,
        default: "",
    },
    paymentType: {
        type: String,
        required: true,
    },
    class: {
        type: String,
        default: "",
    },
});

const deliveryTypeNumber = computed(() => {
    if (props.deliveryRoomNumber !== null) {
        return props.deliveryRoomNumber;
    } else if (props.deliveryTableNumber !== null) {
        return props.deliveryTableNumber;
    } else return "";
});

const deliveryTypeIcon = computed(() => {
    if (props.deliveryType === "delivery_address") {
        return IconTruck;
    } else if (props.deliveryType === "delivery_table") {
        return IconDesk;
    } else if (props.deliveryType === "delivery_room") {
        return IconDoor;
    } else if (props.deliveryType === "delivery_personal_pickup") {
        return IconWalk;
    }

    return IconX;
});

const payTypeIcon = computed(() => {
    if (props.paymentType === "cash") {
        return IconCoinFilled;
    } else if (props.paymentType === "card") {
        return IconCreditCard;
    } else if (props.paymentType === "card_delivery") {
        return IconMoped;
    } else if (props.paymentType === "card_p24") {
        return IconCash;
    } else if (props.paymentType === "card_tpay") {
        return IconCreditCardFilled;
    } else if (props.paymentType === "hotel_bill") {
        return IconBuilding;
    }

    return IconX;
});

const parentClass = computed(() => {
    return `flex gap-3 justify-between ${props.class ?? ""}`;
});
</script>

<template>
    <ul :class="parentClass">
        <li class="flex gap-2 items-center">
            <deliveryTypeIcon />
            <span class="text-blue-600 dark:text-blue-500">
                {{ $t(deliveryType)
                }}{{ deliveryTypeNumber ? `: ${deliveryTypeNumber}` : "" }}
            </span>
        </li>
        <li class="flex gap-2 items-center">
            <payTypeIcon />
            <span class="text-blue-600 dark:text-blue-500">{{
                $t(paymentType)
            }}</span>
        </li>
    </ul>
</template>
