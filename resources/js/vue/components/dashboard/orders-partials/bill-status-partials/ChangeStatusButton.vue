<script setup>

import {computed} from "vue";

const props = defineProps({
    onClick: {
        type: Function,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    class: {
        type: String,
        required: false
    }
});

const mergedClass = computed(() => {
    return `flex h-12 w-12 items-center justify-center rounded-full text-gray-50 ${props.class}`;
});
const changeStatusHandler = () => {
    props.onClick();
}
</script>

<template>
        <div class="md:col-span-2 md:row-span-1 max-md:row-start-2 max-md:row-end-3 col-span-1 group flex cursor-pointer flex-col items-center justify-center gap-2"
            @click="changeStatusHandler">
            <div :class="mergedClass">
                <slot></slot>
            </div>
            <span class="text-center text-sm">{{title}}</span>
        </div>
</template>

<style scoped>

</style>
