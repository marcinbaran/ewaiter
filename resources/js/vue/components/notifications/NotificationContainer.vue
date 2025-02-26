<script setup lang="ts">
import { ref } from "vue";
import NotificationHeader from "./notification-partials/NotificationHeader.vue";
import NotificationBody from "./notification-partials/NotificationBody.vue";
import Spinner from "../ui/Spinner.vue";

const props = defineProps<{
    notifications: Array<any>;
    notificationsLength: number;
    markNotificationAsRead: (id: number) => void;
    markAllNotificationsAsRead: () => void;
    isLoadingNotifications: boolean;
}>();

const isNotificationHeaderShown = ref<boolean>(true);

const showNotificationHeader = () => {
    isNotificationHeaderShown.value = true;
};
const hideNotificationHeader = () => {
    isNotificationHeaderShown.value = false;
};
</script>

<template>
    <div
        class="h-80 w-[calc(100vw-5rem)] overflow-hidden md:w-96 absolute right-0 rounded-lg border border-gray-300 bg-gray-200 p-2 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
    >
        <Transition name="fade">
            <notification-header
                v-if="isNotificationHeaderShown"
                :mark-all-notifications-as-read="markAllNotificationsAsRead"
                :notifications-length="notificationsLength"
            />
        </Transition>
        <div class="w-full flex justify-center items-center">
            <Spinner v-if="isLoadingNotifications" />
        </div>
        <div
            v-if="!isLoadingNotifications && notificationsLength === 0"
            class="w-full flex justify-center items-center border-gray-300 dark:border-gray-700 border-t-[1px]"
        >
            <span class="text-sm dark:text-gray-400 pt-2">
                {{ $t("no_notifications") }}
            </span>
        </div>
        <Transition name="expand">
            <notification-body
                :showNotificationHeaderHandler="showNotificationHeader"
                :hide-notification-header-handler="hideNotificationHeader"
                :mark-notification-as-read="markNotificationAsRead"
                :notifications="notifications"
            />
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.2s ease;
    transform: translateY(0);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-100%);
}
</style>
