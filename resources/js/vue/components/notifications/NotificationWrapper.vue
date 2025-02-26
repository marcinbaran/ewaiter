<script setup lang="ts">
import { IconBell } from "@tabler/icons-vue";
import NotificationContainer from "./NotificationContainer.vue";
import { computed, onMounted, ref } from "vue";
import { getHostname, sendRequest } from "../../../helpers.js";
import Button from "../dashboard/orders-partials/Button.vue";

const iconBellRef = ref<HTMLElement>(null);
const notificationContainerRef = ref<HTMLElement>(null);
const isNotificationContainerShown = ref(false);
const isLoadingNotifications = ref(false);
const isFirstFetch = ref(true);
const notifications = ref<Array<any>>([]);

const fetchNotifications = () => {
    if (isFirstFetch.value && notifications.value.length === 0) {
        isLoadingNotifications.value = true;
        isFirstFetch.value = false;
    }
    const url = `${getHostname()}/admin/dashboard/notifications`;
    sendRequest(url, "GET")
        .then((response) => {
            isLoadingNotifications.value = false;
            notifications.value = response;
        })
        .catch((error) => {
            console.log(error);
        });
};

const markNotificationAsRead = (id: number) => {
    const url = `${getHostname()}/admin/dashboard/notifications/${id}/mark-as-read`;
    sendRequest(url, "PUT")
        .then(() => {
            fetchNotifications();
        })
        .catch((error) => {
            console.log(error);
        });
};

const markAllNotificationsAsRead = () => {
    const url = `${getHostname()}/admin/dashboard/notifications/mark-all-as-read`;
    notifications.value = [];
    sendRequest(url, "PUT")
        .then(() => {
            fetchNotifications();
        })
        .catch((error) => {
            console.log(error);
        });
};

const notificationsLength = computed(() => {
    return notifications.value.length;
});

const isBadgeShown = computed(() => {
    return notifications.value.length > 0;
});

onMounted(() => {
    fetchNotifications();
    setInterval(() => {
        fetchNotifications();
    }, 5000);
    iconBellRef.value?.addEventListener("click", () => {
        isNotificationContainerShown.value =
            !isNotificationContainerShown.value;
    });
    window.addEventListener("click", (event) => {
        const containsButton = iconBellRef.value?.contains(
            event.target as Node,
        );
        const notificationContainerRect =
            notificationContainerRef.value?.$el.getBoundingClientRect();
        const isClickedInsideContainer =
            event.clientX >= notificationContainerRect?.left &&
            event.clientX <= notificationContainerRect?.right &&
            event.clientY >= notificationContainerRect?.top &&
            event.clientY <= notificationContainerRect?.bottom;
        if (!containsButton && !isClickedInsideContainer) {
            isNotificationContainerShown.value = false;
        }
    });
});
</script>

<template>
    <div class="relative w-auto h-fit">
        <div>
            <div
                id="tooltip-notifications"
                role="tooltip"
                class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-600 px-3 py-2 text-center text-sm font-medium text-gray-50 opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700"
            >
                {{ $t("notifications") }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>

            <button
                ref="iconBellRef"
                class="relative rounded-lg p-1 text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50"
            >
                <span
                    v-if="isBadgeShown"
                    class="bg-red-700 rounded-full flex absolute top-0 right-0 text-xs text-gray-50 justify-center items-center p-2.5 w-4 h-4"
                    >{{ notificationsLength }}</span
                >
                <IconBell
                    data-tooltip-target="tooltip-notifications"
                    class="dark:stroke-gray-400 w-7 h-7 cursor-pointer"
                />
            </button>
        </div>
        <notification-container
            ref="notificationContainerRef"
            v-show="isNotificationContainerShown"
            :notifications-length="notificationsLength"
            :mark-notification-as-read="markNotificationAsRead"
            :mark-all-notifications-as-read="markAllNotificationsAsRead"
            :notifications="notifications"
            :is-loading-notifications="isLoadingNotifications"
        />
    </div>
</template>

<style scoped></style>
