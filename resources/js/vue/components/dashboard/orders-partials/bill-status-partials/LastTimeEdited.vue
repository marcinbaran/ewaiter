<script setup>
const props = defineProps({
    updatedAt: {
        type: String,
        required: true
    }
});
</script>

<template>
<span class="text-sm text-gray-600 dark:text-gray-400">
    {{ $t("edited") }} {{ updatedAt }} {{ $t("ago") }}
</span>
</template>

<style scoped>

</style>
