<script setup>
import { computed, ref, watch } from "vue";

import Navigation from "./hero-widget-partials/Navigation.vue";
import NewOrders from "./NewOrders.vue";
import CurrentOrders from "./CurrentOrders.vue";
import Notifications from "./Notifications.vue";
import unseenOrders from "../../assets/unseen-order-sound.mp3";
import { useSound } from "@vueuse/sound";

const { play, stop } = useSound(unseenOrders, {
    volume: 1,
});

const section = {
    newOrders: 1,
    currentOrders: 2,
    notifications: 3,
};

const currentSection = ref(section.newOrders);
const sectionNotificationCounts = ref({
    newOrders: 0,
    currentOrders: 0,
    notifications: 0,
});

const playSoundInteral = ref(null);
const isIntervalSet = ref(false);

const newOrdersOrNotificationsHasItems = computed(() => {
    const { newOrders, notifications } = sectionNotificationCounts.value;
    return newOrders > 0 || notifications > 0;
});

watch(
    () => sectionNotificationCounts.value,
    () => {
        if (newOrdersOrNotificationsHasItems.value && !isIntervalSet.value) {
            playSoundInteral.value = setInterval(() => {
                play();
            }, 5000);
            isIntervalSet.value = true;
        } else if (
            !newOrdersOrNotificationsHasItems.value &&
            isIntervalSet.value
        ) {
            clearInterval(playSoundInteral.value);
            isIntervalSet.value = false;
        }
    },
    { deep: true },
);

const changeSection = (section) => {
    currentSection.value = section;
};

const setSectionNotificationCount = (section, count) => {
    sectionNotificationCounts.value[section] = count;
};
</script>

<template>
    <div class="hero-widget w-full h-full flex flex-col">
        <Navigation
            :section="section"
            :currentSection="currentSection"
            :sectionNotificationCounts="sectionNotificationCounts"
            :changeSection="changeSection"
        />
        <div
            class="hero-widget__sections flex-1 overflow-hidden rounded-b-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50"
        >
            <div
                v-show="currentSection === section.newOrders"
                class="hero-widget__section w-full h-full"
            >
                <NewOrders
                    sectionId="newOrders"
                    :notificationCountHandler="setSectionNotificationCount"
                />
            </div>
            <div
                v-show="currentSection === section.currentOrders"
                class="hero-widget__section w-full h-full"
            >
                <CurrentOrders
                    sectionId="currentOrders"
                    :notificationCountHandler="setSectionNotificationCount"
                />
            </div>
            <div
                v-show="currentSection === section.notifications"
                class="hero-widget__section w-full h-full"
            >
                <Notifications
                    sectionId="notifications"
                    :notificationCountHandler="setSectionNotificationCount"
                />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
