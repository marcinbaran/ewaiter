<script setup>
import { computed, defineProps } from "vue";

import NotificationCountBadge from "../../../ui/NotificationCountBadge.vue";

const props = defineProps({
    class: {
        type: String,
        default: "",
    },
    isActive: {
        type: Boolean,
        default: false,
    },
    notificationCount: {
        type: Number,
        default: 0,
    },
    clickHandler: {
        type: Function,
        default: () => console.log("clicked"),
    },
});

const navigationButtonClasses = computed(() => {
    let classes = `navigation__button px-4 py-2 border-b-2 relative ${
        props.class ?? ""
    }`;
    if (props.isActive === true) {
        classes += ` border-primary-900 dark:border-primary-700 text-primary-900 dark:text-primary-700`;
    } else {
        classes += ` border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50`;
    }
    return classes;
});
</script>

<template>
    <button :class="navigationButtonClasses" @click="props.clickHandler">
        <NotificationCountBadge
            class="absolute top-0 right-0"
            :notificationCount="props.notificationCount"
            v-show="props.notificationCount > 0"
        />
        <slot></slot>
    </button>
</template>
