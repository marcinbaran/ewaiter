<script setup>
const props = defineProps({
    name: {
        type: String,
        required: true
    },
    quantity: {
        type: Number,
        required: true
    },
    price: {
        type: String,
        required: true
    },
    additions: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-col gap-1 items-start">
        <div class="font-bold break-all">{{ name }} x{{ quantity }} - {{ price }} PLN</div>
        <ul class="list-disc ml-4">
            <li v-for="addition in additions" class="break-all text-gray-600 dark:text-gray-400">
                {{ addition.name }}, {{ addition.price }} PLN, {{ addition.quantity }} {{ $t("pc") }}.
            </li>
        </ul>
    </div>
</template>
