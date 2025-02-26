<script setup>
import { defineProps, ref } from "vue";

import { IconSearch, IconReload } from "@tabler/icons-vue";

const props = defineProps({
    refreshOrders: Function,
});

const isLoading = ref(false);
const refreshHandler = async () => {
    isLoading.value = true;
    try {
        await props.refreshOrders();
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div
        @click="refreshHandler"
        class="orders-zero-data w-full h-full flex flex-col gap-6 justify-center items-center cursor-pointer rounded-lg"
    >
        <IconSearch size="4rem" />
        <p class="text-lg">{{ $t("there_will_be_new_orders_here") }}</p>
        <div class="flex gap-2">
            <IconReload size="1.5rem" />
            {{ $t("click_here_to_refresh") }}
        </div>
        <div class="flex items-center justify-center">
            <div v-if="isLoading" class="loader"></div>
        </div>
    </div>
</template>
