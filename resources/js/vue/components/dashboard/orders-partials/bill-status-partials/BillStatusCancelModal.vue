<script setup>
const props = defineProps({
     changeBillStatus: {
        type: Function,
        required: true
     },
    hideModal: {
        type: Function,
        required: true
    }
});

const changeBillStatusHandler = () => {
    props.changeBillStatus(4);
    props.hideModal();
}

const hideModalHandler = () => {
    props.hideModal();
}

</script>

<template>
    <div class="absolute flex w-full h-full z-30 rounded-lg backdrop-blur"
         @click="hideModalHandler"></div>
    <div class="absolute left-1/2 top-1/2 z-30 flex p-4 -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center gap-4 rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50">
        <p class="text-center">
            {{ $t("are_you_sure_that_you_want_to_cancel_order") }}
        </p>
        <div class="flex items-center justify-center gap-4">
            <button
                class="px-4 py-2 rounded-lg bg-red-700 text-gray-50"
                @click="hideModalHandler">
                {{ $t("no") }}
            </button>
            <button
                class="px-4 py-2 rounded-lg bg-green-700 text-gray-50"
                @click="changeBillStatusHandler">
                {{ $t("yes") }}
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>
