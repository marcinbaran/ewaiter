<script setup>
import { computed, onBeforeMount, onUpdated, ref } from "vue";
import {
    IconArrowNarrowDown,
    IconArrowNarrowUp,
    IconBounceRight,
} from "@tabler/icons-vue";
import { trans } from "laravel-vue-i18n";

import Toast from "../../../additional-scripts/toast";
import { getHostname, getTokenCSRF } from "../../../helpers";

import Notification from "./notifications-partials/Notification.vue";
import Spinner from "../ui/Spinner.vue";

const props = defineProps({
    sectionId: {
        type: String,
        required: true,
    },
    notificationCountHandler: {
        type: Function,
        required: true,
    },
});

const csrfToken = ref(getTokenCSRF());
const notificationsListTranslateY = ref(0);
const isUpButtonDisabled = ref(false);
const isDownButtonDisabled = ref(false);
const isNewNotificationPrompt = ref(false);
const isSpinnerVisible = ref(true);

const notifications = ref([]);
const newNotifications = ref([]);
const newNotificationsCount = computed(() => newNotifications.value.length);

const notificationsRef = ref([]);
const notificationsListRef = ref(null);
const spinnerRef = ref(null);

const getNotifications = async () => {
    const url = `${getHostname()}/admin/dashboard/notifications`;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json charset=utf-8",
            Accept: "application/json",
        },
    });
    const json = await response.json();
    return json;
};

const markAsRead = (id) => {
    const url = `${getHostname()}/admin/dashboard/notifications/${id}/mark-as-read`;
    fetch(url, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json charset=utf-8",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken.value,
        },
    }).then((data) => {
        if (data.ok) {
            notifications.value = notifications.value.filter(
                (notification) => notification.id !== id,
            );
            newNotifications.value = newNotifications.value.filter(
                (newId) => newId !== id,
            );
            props.notificationCountHandler(
                props.sectionId,
                newNotificationsCount.value,
            );
            setNewNotificiationPrompt();
            Toast.success(trans("notification_marked_as_read"), true, 5000);
        }
    });
};

const scrollTop = () => {
    notificationsListTranslateY.value = 0;
    checkButtonsDisabled();
    setNewNotificiationPrompt();
};

const scrollUp = () => {
    const notification =
        notificationsRef.value.length > 0 && notificationsRef.value[0].$el;
    if (notification) {
        if (
            notificationsListTranslateY.value > 0 ||
            (notificationsListTranslateY.value <= 0 &&
                notificationsListTranslateY.value > -notification.clientHeight)
        ) {
            notificationsListTranslateY.value = 0;
        } else {
            notificationsListTranslateY.value += notification.clientHeight;
        }
    }
    checkButtonsDisabled();
    setNewNotificiationPrompt();
};

const scrollDown = () => {
    const notification =
        notificationsRef.value.length > 0 && notificationsRef.value[0].$el;
    if (notification) {
        const elementsHeight =
            notifications.value.length * notification.clientHeight;
        const maxOffset =
            elementsHeight - notificationsListRef.value.clientHeight;
        if (
            notificationsListTranslateY.value - notification.clientHeight <=
            -maxOffset
        ) {
            notificationsListTranslateY.value = -maxOffset;
        } else {
            notificationsListTranslateY.value -= notification.clientHeight;
        }
    }
    checkButtonsDisabled();
    setNewNotificiationPrompt();
};

const checkButtonsDisabled = () => {
    const notification =
        notificationsRef.value.length > 0 && notificationsRef.value[0].$el;
    if (notification) {
        const elementsHeight =
            notifications.value.length * notification.clientHeight;
        const maxOffset =
            elementsHeight - notificationsListRef.value.clientHeight;
        if (elementsHeight < notificationsListRef.value.clientHeight) {
            isUpButtonDisabled.value = true;
            isDownButtonDisabled.value = true;
        } else if (notificationsListTranslateY.value === 0) {
            isUpButtonDisabled.value = true;
            isDownButtonDisabled.value = false;
        } else if (notificationsListTranslateY.value === -maxOffset) {
            isUpButtonDisabled.value = false;
            isDownButtonDisabled.value = true;
        } else {
            isUpButtonDisabled.value = false;
            isDownButtonDisabled.value = false;
        }
    }
};

const setNewNotificiationPrompt = () => {
    isNewNotificationPrompt.value =
        newNotifications.value.length > 0 &&
        notificationsListTranslateY.value < 0;
};

const refreshNotifications = async (firstCheck = false) => {
    const oldNotificationArray = [...notifications.value];
    const newNotificationArray = await getNotifications();
    const newNotificationIds = [...newNotifications.value];
    if (!firstCheck) {
        newNotificationIds.push(
            ...getNewNotificationIds(
                oldNotificationArray,
                newNotificationArray,
            ),
        );
    }
    notifications.value = formatNotificiations(
        newNotificationArray,
        newNotificationIds,
    );
    newNotifications.value = newNotificationIds;
    props.notificationCountHandler(
        props.sectionId,
        newNotificationsCount.value,
    );
    setNewNotificiationPrompt();
    return notifications.value;
};

const getNewNotificationIds = (oldNotifications, newNotifications) => {
    const oldNotificationIds = oldNotifications.map(
        (notification) => notification.id,
    );
    const newNotificationsIds = newNotifications
        .map((notification) => notification.id)
        .filter((id) => !oldNotificationIds.includes(id));
    return newNotificationsIds;
};

const formatNotificiations = (notifications, newNotificationIds) => {
    const notificationsArray = notifications.map((notification) => {
        return {
            ...notification,
            isNew: newNotificationIds.includes(notification.id),
        };
    });
    return notificationsArray;
};

const changeSpinnerVisibility = (visibility) => {
    if (visibility === false) {
        spinnerRef.value.classList.add("fade-in");
        setTimeout(() => {
            isSpinnerVisible.value = false;
        }, 500);
    } else {
        isSpinnerVisible.value = true;
    }
};

onBeforeMount(async () => {
    refreshNotifications(true).then(() => {
        checkButtonsDisabled();
        changeSpinnerVisibility(false);
    });
    setInterval(async () => {
        refreshNotifications();
    }, 5000);
});

onUpdated(() => {
    checkButtonsDisabled();
});
</script>

<template>
    <div
        class="dashboard-notifications w-full h-full flex relative overflow-hidden"
    >
        <ul
            class="dashboard-notifications--list flex-1 h-full pl-3 transition-transform"
            :style="{
                transform: `translateY(${notificationsListTranslateY}px)`,
            }"
            ref="notificationsListRef"
            v-if="notifications.length > 0"
        >
            <Notification
                v-for="notification in notifications"
                :key="notification.id"
                :title="notification.title"
                :description="notification.description"
                :time="notification.created_at"
                :isNew="notification.isNew"
                :onClick="() => markAsRead(notification.id)"
                ref="notificationsRef"
            />
        </ul>
        <div
            class="dashboard-notifications--empty flex items-center justify-center flex-1 h-full"
            v-else
        >
            <p class="text-gray-900 dark:text-gray-50">
                {{ $t("no_notifications") }}
            </p>
        </div>
        <div class="dashboard-notifications--buttons p-3 flex flex-col gap-6">
            <button
                class="scroll-up flex-1 text-gray-900 dark:text-gray-50 bg-gray-300 dark:bg-gray-600 rounded-lg px-2 hover:text-primary-900 dark:hover:text-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
                @click="scrollUp"
                :disabled="isUpButtonDisabled"
            >
                <IconArrowNarrowUp />
            </button>
            <button
                class="scroll-down flex-1 text-gray-900 dark:text-gray-50 bg-gray-300 dark:bg-gray-600 rounded-lg px-2 hover:text-primary-900 dark:hover:text-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
                @click="scrollDown"
                :disabled="isDownButtonDisabled"
            >
                <IconArrowNarrowDown />
            </button>
        </div>
        <div
            class="dashboard-notification--new absolute top-0 left-0 w-full text-gray-50 bg-red-500 bg-opacity-75 p-2 rounded-b-lg text-sm flex gap-6 justify-between"
            v-if="isNewNotificationPrompt"
        >
            <p class="new-notification--text">
                {{
                    `${$t("you_have")} ${newNotificationsCount} ${
                        newNotificationsCount === 1
                            ? $t("new_notification").toLowerCase()
                            : $t("new_notifications").toLowerCase()
                    }.`
                }}
            </p>
            <span
                class="new-notification--button flex gap-1 items-center hover:opacity-80 cursor-pointer"
                @click="scrollTop"
                >{{ $t("go") }} <IconBounceRight size="1rem"
            /></span>
        </div>
        <div
            class="dashboard-notifications--spinner absolute inset-0 flex items-center justify-center backdrop-blur transition-opacity"
            v-if="isSpinnerVisible"
            ref="spinnerRef"
        >
            <Spinner />
        </div>
    </div>
</template>

<style scoped>
div.dashboard-notifications--spinner {
    animation: fade-out 0.5s ease-in-out forwards paused;
}

div.dashboard-notifications--spinner.fade-in {
    animation-play-state: running;
}

@keyframes fade-out {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}
</style>
