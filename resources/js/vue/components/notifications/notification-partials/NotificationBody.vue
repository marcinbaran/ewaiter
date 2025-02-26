<script setup lang="ts">
import NotificationItem from "./NotificationItem.vue";
import { onMounted, ref } from "vue";

const props = defineProps<{
    showNotificationHeaderHandler: () => void;
    hideNotificationHeaderHandler: () => void;
    markNotificationAsRead: (id: number) => void;
    notifications: Array<any>;
}>();

const intersectionTargetHideNavbarRef = ref<HTMLElement>(null);

const createObserver = () => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    props.hideNotificationHeaderHandler();
                } else {
                    props.showNotificationHeaderHandler();
                }
            });
        },
        {
            root: null,
            rootMargin: "0px",
            threshold: 0.1,
        },
    );
    observer.observe(intersectionTargetHideNavbarRef.value);
};

onMounted(() => {
    createObserver();
});
</script>

<template>
    <perfect-scrollbar
        class="flex transition-all duration-100 w-full h-full flex-col divide-y divide-gray-300 overflow-y-hidden pt-2 pb-2 dark:divide-gray-700"
    >
        <div
            ref="intersectionTargetHideNavbarRef"
            class="w-full h-px opacity-0"
        ></div>
        <notification-item
            v-for="notification in notifications"
            :key="notification.id"
            :id="notification.id"
            :title="notification.title"
            :link="notification.link ? notification.link : '#'"
            :body="notification.description"
            :created-at="notification.created_at"
            :mark-notification-as-read="markNotificationAsRead"
        />
    </perfect-scrollbar>
</template>

<style scoped></style>
