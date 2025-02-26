<script setup>
import { computed, defineProps } from "vue";

import NavigationButton from "./navigation-partials/NavigationButton.vue";
import { IconArrowsMinimize } from "@tabler/icons-vue";
import { getHostname } from "../../../../helpers";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    currentSection: {
        type: Number,
        required: true,
    },
    sectionNotificationCounts: {
        type: Object,
        required: true,
    },
    changeSection: {
        type: Function,
        required: true,
    },
    classes: {
        type: String,
        default: "",
    },
});

const heroWidgetNavigationClasses = computed(() => {
    const classes = `hero-widget__navigation flex gap-2 ${props.classes ?? ""}`;
    return classes;
});

const isExitFullscreenButtonShown = window.location.href.includes("fullscreen");

const exitFullscreenHandler = () => {
    window.location.href = `${getHostname()}/admin`;
};
</script>

<template>
    <div :class="heroWidgetNavigationClasses">
        <NavigationButton
            :notificationCount="sectionNotificationCounts.newOrders"
            :isActive="currentSection === section.newOrders"
            :clickHandler="() => props.changeSection(props.section.newOrders)"
        >
            {{ $t("new_orders") }}
        </NavigationButton>
        <NavigationButton
            :notificationCount="props.sectionNotificationCounts.currentOrders"
            :isActive="props.currentSection === props.section.currentOrders"
            :clickHandler="
                () => props.changeSection(props.section.currentOrders)
            "
        >
            {{ $t("current_orders") }}
        </NavigationButton>
        <NavigationButton
            :notificationCount="props.sectionNotificationCounts.notifications"
            :isActive="props.currentSection === props.section.notifications"
            :clickHandler="
                () => props.changeSection(props.section.notifications)
            "
        >
            {{ $t("notifications") }}
        </NavigationButton>
        <NavigationButton
            v-if="isExitFullscreenButtonShown"
            :clickHandler="exitFullscreenHandler"
            class="ml-auto"
        >
            <IconArrowsMinimize size="1.25rem" />
        </NavigationButton>
    </div>
</template>
