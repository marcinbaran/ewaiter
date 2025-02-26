<script setup>
import { IconBell } from "@tabler/icons-vue";
import { IconBellFilled } from "@tabler/icons-vue";
import { IconEyeCheck } from "@tabler/icons-vue";
import { onMounted, ref } from "vue";

const props = defineProps({
    id: String,
    title: String,
    link: String,
    body: String,
    createdAt: String,
    markNotificationAsRead: Function,
});
const markedAsRead = ref(false);

const markAsRead = (event) => {
    if (event) {
        event.stopPropagation();
    }
    props.markNotificationAsRead(props.id);
    markedAsRead.value = true;
};

const redirectAndMarkAsRead = () => {
    window.location.href = props.link;
    markAsRead();
};
</script>

<template>
    <Transition>
        <div
            @click="redirectAndMarkAsRead"
            v-if="!markedAsRead"
            class="cursor-pointer flex w-full flex-row items-center justify-between gap-2 p-2 hover:rounded-lg hover:bg-gray-600 hover:text-gray-50 dark:hover:bg-gray-700 dark:hover:text-gray-50"
        >
            <icon-bell class="h-6 w-6" />
            <div class="flex flex-1 flex-col text-sm overflow-hidden">
                <span class="text-md capitalize">
                    {{ props.title }}
                </span>
                <span class="w-full break-words">
                    {{ props.body }}
                </span>
                <span class="mt-1 text-xs">
                    {{ props.createdAt }}
                </span>
            </div>
            <icon-eye-check
                @click="markAsRead"
                class="h-5 w-5 cursor-pointer"
            />
        </div>
    </Transition>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: all 0.2s ease;
    transform: translateY(0);
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
    transform: translateY(-100%);
}
</style>
