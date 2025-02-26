<script setup>
import { computed, defineProps } from "vue";

const props = defineProps({
    class: String,
});

const spinnerClass = computed(() => {
    return `spinner-container ${props.class ?? ""}`;
});
</script>

<template>
    <div :class="spinnerClass">
        <div class="spinner w-12 h-12 rounded-full relative animate-spin">
            <div
                class="spinner__outer-ring absolute inset-0 rounded-full border-[5px] border-gray-600 dark:border-gray-400"
            ></div>
            <div
                class="spinner__inner-ring absolute inset-2 rounded-full border-[5px] border-primary-900 dark:border-primary-700"
            ></div>
        </div>
    </div>
</template>

<style scoped>
div.spinner__outer-ring,
div.spinner__inner-ring {
    animation: prixClipFix 2s linear both;
}
div.spinner__inner-ring {
    transform: rotate3d(90, 90, 0, 180deg);
}

@keyframes prixClipFix {
    0% {
        clip-path: polygon(50% 50%, 0 0, 0 0, 0 0, 0 0, 0 0);
    }
    50% {
        clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 0, 100% 0, 100% 0);
    }
    75%,
    100% {
        clip-path: polygon(
            50% 50%,
            0 0,
            100% 0,
            100% 100%,
            100% 100%,
            100% 100%
        );
    }
}
</style>
