<script setup>
import {computed} from "vue";
import BillElementStatus from "../../../../types/BillElementStatus";

const props = defineProps({
    elementStatus: {
        type: Number,
        required: true,
    },
    class: {
        type: String,
        default: "",
    },
});

const notActivatedStatusPointClass = computed(() => {
    return "bg-white w-6 h-6 relative z-20 rounded-full transform transition-all duration-75";
});

const activatedStatusPointClass = computed(() => {
    return "bg-blue-700 w-6 h-6 relative z-20 rounded-full transform transition-all duration-75 ";
});

const currentActiveStatusPointClass = computed(() => {
    return "bg-blue-700 w-10 h-10 relative z-20 rounded-full transform transition-all duration-75 ";
});

const cancelStatusPointClass = computed(() => {
    return "bg-red-700 w-6 h-6 relative z-20 rounded-full transform transition-all duration-75 ";
});

const complaintStatusPointClass = computed(() => {
    return "bg-yellow-400 w-6 h-6 relative z-20 rounded-full transform transition-all duration-75 ";
});

const pointClass = computed(() => {
    switch (props.elementStatus){
        case BillElementStatus.NotActive:
            return notActivatedStatusPointClass.value;
        case BillElementStatus.Activated:
            return activatedStatusPointClass.value;
        case BillElementStatus.Active:
            return currentActiveStatusPointClass.value;
        case BillElementStatus.Cancelled:
            return cancelStatusPointClass.value;
        case BillElementStatus.Complaint:
            return complaintStatusPointClass.value;
    }
})
</script>

<template>
    <div :class="pointClass">
        <span class="absolute -top-6 left-1/2 -translate-x-1/2 transform text-[0.7rem] md:text-xs font-bold transition-all duration-75" >
            <slot></slot>
        </span>
    </div>
</template>

<style scoped>

</style>
