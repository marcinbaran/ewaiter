<script setup>
import { defineProps, computed } from "vue";

const props = defineProps({
    class: {
        type: String,
        default: "",
    },
});

const parentClass = computed(() => {
    return `vertical-divider w-px bg-gray-300 dark:bg-gray-700 ${props.class}`;
});
</script>

<template>
    <div :class="parentClass"></div>
</template>
