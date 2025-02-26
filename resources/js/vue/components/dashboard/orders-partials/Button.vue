<template>
    <div>
        <button :class="classes" :disabled="disabled" @click="clickHandler">
            <slot></slot>
        </button>
    </div>
</template>

<script lang="ts" setup>
import { computed } from "vue";

const props = defineProps({
    onClick: {
        type: Function,
        default: () => {},
    },
    color: {
        type: String,
        default: "primary",
        validator: (value: String) =>
            value === "primary" || value === "danger" || value === "success",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const clickHandler = () => {
    props.onClick();
};

const classes = computed<String>(() => {
    let classes: String =
        "rounded-lg px-5 py-2 text-gray-50 disabled:opacity-25 disabled:cursor-not-allowed";
    if (props.color === "success") {
        classes +=
            " bg-green-900 dark:bg-green-700 hover:bg-green-800 focus:ring-green-700 dark:focus:ring-green-900";
    } else if (props.color === "danger") {
        classes +=
            " bg-red-700 dark:bg-red-600 hover:bg-red-800 dark:hover:bg-red-700 focus:ring-red-300 dark:focus:ring-red-900";
    } else {
        classes +=
            " bg-blue-700 dark:bg-blue-600 hover:bg-blue-800 dark:hover:bg-blue-700 focus:ring-blue-300 dark:focus:ring-blue-900";
    }
    return classes;
});
</script>

<style scoped></style>
