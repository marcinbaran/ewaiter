<script setup>
import ReadAllButton from "./ReadAllButton.vue";
import { computed, onMounted } from "vue";

const props = defineProps({
    markAllNotificationsAsRead: Function,
    notificationsLength: Number,
});

const showButton = computed(() => {
    return props.notificationsLength > 0;
});
const markAllNotificationsAsReadHandler = () => {
    props.markAllNotificationsAsRead();
};
</script>

<template>
    <div class="flex w-full items-center justify-between p-2">
        <span class="text-primary-900 dark:text-primary-700 font-bold">{{
            $t("notifications")
        }}</span>
        <read-all-button
            @click="markAllNotificationsAsReadHandler"
            v-if="showButton"
        />
    </div>
</template>

<style scoped></style>
