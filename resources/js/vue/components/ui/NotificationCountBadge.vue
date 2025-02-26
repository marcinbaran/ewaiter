<script setup>
import { defineProps, computed } from "vue";

const props = defineProps({
    notificationCount: {
        type: Number,
        required: true,
    },
    class: {
        type: String,
        default: "",
    },
});

const notificationCountClasses = computed(() => {
    const classes = `notification-count w-5 aspect-square rounded-full flex justify-center items-center bg-red-500 text-gray-50 text-xs ${
        props.class ?? ""
    }`;
    return classes;
});
</script>

<template>
    <span :class="notificationCountClasses">{{ notificationCount }}</span>
</template>
