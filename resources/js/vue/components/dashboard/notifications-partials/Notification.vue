<script setup>
import { IconMessage, IconCheck, IconCircleFilled } from "@tabler/icons-vue";
import { computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        default: "Title",
    },
    description: {
        type: String,
        default: "Description",
    },
    time: {
        type: String,
        default: "few seconds ago",
    },
    isNew: {
        type: Boolean,
        default: false,
    },
    onClick: {
        type: Function,
        default: () => {},
    },
});

const listElementClasses = computed(() => {
    const initialClasses = [
        "dashboard-notification",
        "relative",
        "py-3",
        "px-4",
        "flex",
        "gap-3",
        "rounded-lg",
        "text-gray-600",
        "dark:text-gray-400",
        "h-24",
        "cursor-pointer",
        "group/notification",
        "hover:text-gray-900",
        "dark:hover:text-gray-50",
    ];
    if (props.isNew) {
        initialClasses.push(
            "font-bold dark:font-normal text-gray-900 dark:text-gray-50",
        );
    }
    return initialClasses;
});
</script>

<template>
    <li :class="listElementClasses" @click="onClick">
        <IconCircleFilled
            color="rgba(255, 0, 0, 0.75)"
            size="0.75rem"
            class="absolute top-4 left-0"
            v-show="props.isNew"
        />
        <div class="notification--icon flex justify-center items-center mr-2">
            <IconMessage />
        </div>
        <div class="notification--content flex-1">
            <p class="notification--title">{{ title }}</p>
            <p class="notification--description text-md">{{ description }}</p>
            <p class="notification--time text-xs mt-1">{{ time }}</p>
        </div>
        <div
            class="notification--button flex justify-center items-center group-hover/notification:text-primary-900 dark:group-hover/notification:text-primary-700"
        >
            <IconCheck />
        </div>
    </li>
</template>
