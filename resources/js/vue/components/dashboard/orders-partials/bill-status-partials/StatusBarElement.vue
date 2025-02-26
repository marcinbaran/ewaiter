<script setup>
import {computed} from "vue";
import BillElementStatus from "../../../../types/BillElementStatus";

const props = defineProps({
    elementStatus: {
        type: Number,
        required: true
    },
    class: {
        type: String,
        default: ""
    }
});
const statusBarActive = computed(() => {
    return "h-full w-full bg-blue-700";
});

const statusBarInactive = computed(() => {
    return "h-full w-0 bg-white";
});

const statusBarNext = computed(() => {
    return "h-full w-0 bg-white status-bar";
});

const statusBarCancel = computed(() => {
    return "h-full w-full bg-red-700";
});

const statusBarComplaint = computed(() => {
    return "h-full w-full bg-yellow-400";
});

const statusBarClass = computed(() => {
    switch (props.elementStatus){
        case BillElementStatus.NotActive:
            return statusBarInactive.value;
        case BillElementStatus.Next:
            return statusBarNext.value;
        case BillElementStatus.Active:
            return statusBarActive.value;
        case BillElementStatus.Cancelled:
            return statusBarCancel.value;
        case BillElementStatus.Complaint:
            return statusBarComplaint.value;
    }
});

const mergedClass = computed(() => {
    return `absolute top-1/2 z-10 h-[5px] w-1/3 -translate-y-1/2 overflow-hidden bg-white ${props.class}`;
});

</script>

<template>
    <div :class="mergedClass" >
        <div :class="statusBarClass"></div>
    </div>
</template>

<style scoped>

</style>
