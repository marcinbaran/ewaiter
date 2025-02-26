<script setup>

import { computed } from "vue";

const props = defineProps({
    bundle: {
        type: Object,
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

const dishes = computed(() => {
    const mappedDishes = (props.bundle.dishes ?? []).map((dish) => ({ id: dish.id, name: dish.name, additions: [] }));
    props.additions.forEach((addition) => {
        const dish = mappedDishes.find((dish) => dish.id === addition.dish_id);
        dish && dish.additions.push(addition);
    });
    return mappedDishes;
});

</script>

<template>
    <div class="flex flex-col items-start">
        <div class="font-bold break-all">{{ bundle.name }} x{{ quantity }} - {{ price }} PLN</div>
        <div class="flex flex-col gap-1 break-all">
            {{ $t("bundle_includes") }}:
            <div v-for="dish in dishes" class="break-all text-gray-600 dark:text-gray-400">
                <span class="font-semibold">{{ dish.name }}</span>
                <ul class="list-disc ml-4">
                    <li v-for="addition in dish.additions" class="break-all text-gray-600 dark:text-gray-400">
                        {{ addition.name }}, {{ addition.price }} PLN, {{ addition.quantity }} {{ $t("pc") }}.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
