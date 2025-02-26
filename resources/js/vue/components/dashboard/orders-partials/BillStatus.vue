<script setup>
import { computed, defineProps, onMounted, ref, watch } from "vue";
import { formatDistance } from "date-fns";
import { enUS, pl } from "date-fns/locale";
import { trans } from "laravel-vue-i18n";
import { IconChevronRight, IconX } from "@tabler/icons-vue";
import BillStatus from "../../../types/BillStatus";
import BillElementStatus from "../../../types/BillElementStatus";

import Toast from "../../../../additional-scripts/toast";
import { sendRequest as changeBillStatus } from "../../../../helpers.js";
import BillStatusCancelModal from "./bill-status-partials/BillStatusCancelModal.vue";
import LastTimeEdited from "./bill-status-partials/LastTimeEdited.vue";
import ChangeStatusButton from "./bill-status-partials/ChangeStatusButton.vue";
import PointElement from "./bill-status-partials/PointElement.vue";
import StatusBarElement from "./bill-status-partials/StatusBarElement.vue";

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    status: {
        type: Number,
        required: true
    },
    refreshOrders: {
        type: Function
    },
    class: {
        type: String,
        default: ""
    }
});

const currentStatus = ref(props.order.status);
const isSending = ref(false);
const showModal = ref(false);
const currentLocale = ref(document.documentElement.lang);
const locale = ref(pl);
const mergedClasses = computed(
    () =>
        `bill-status relative h-full flex flex-col items-center text-gray-900 dark:text-gray-50 ${
            props.class ?? ""
        }`
);

const isStatusReleased = computed(
    () => currentStatus.value === BillStatus.Released
);

const showModalHandler = () => {
    showModal.value = true;
};
const hideModalHandler = () => {
    showModal.value = false;
};

const formatDistanceWithLocale = (date) => {
    switch (currentLocale.value) {
        case "pl":
            locale.value = pl;
            break;
        case "en":
            locale.value = enUS;
            break;
    }
    return formatDistance(new Date(), new Date(date), {
        locale: locale.value
    });
};

const distanceFormat = computed(() => {
    return formatDistanceWithLocale(props.order.updatedAt);
});

const elementsStatus = ref([
    {
        pointClass: BillElementStatus.NotActive
    },
    {
        pointClass: BillElementStatus.NotActive,
        statusBar: BillElementStatus.NotActive
    },
    {
        pointClass: BillElementStatus.NotActive,
        statusBar: BillElementStatus.NotActive
    },
    {
        pointClass: BillElementStatus.NotActive,
        statusBar: BillElementStatus.NotActive
    }
]);

const nextStatusText = computed(() => {
    switch (currentStatus.value) {
        case BillStatus.Accepted:
            return trans("order_ready");
        case BillStatus.Ready:
            return trans("release_order");
        case BillStatus.Released:
            return trans("order_released");
    }
});

const setStatus = () => {
    elementsStatus.value.forEach((element, index) => {
        if (index === currentStatus.value) {
            element.pointClass = BillElementStatus.Active;
            element.statusBar = BillElementStatus.Active;
        } else if (index <= currentStatus.value) {
            element.pointClass = BillElementStatus.Activated;
            element.statusBar = BillElementStatus.Active;
        } else {
            element.pointClass = BillElementStatus.NotActive;
            element.statusBar = BillElementStatus.NotActive;
        }
    });

    if (currentStatus.value >= 3) {
        props.refreshOrders();
    }

    if (currentStatus.value < 3) {
        elementsStatus.value[currentStatus.value + 1].statusBar =
            BillElementStatus.Next;
    }

    if (currentStatus.value === BillStatus.Cancelled) {
        props.refreshOrders();
        elementsStatus.value.forEach((element) => {
            element.pointClass = BillElementStatus.Cancelled;
            element.statusBar = BillElementStatus.Cancelled;
        });
    }

    if (currentStatus.value === BillStatus.Complaint) {
        elementsStatus.value.forEach((element) => {
            element.pointClass = BillElementStatus.Complaint;
            element.statusBar = BillElementStatus.Complaint;
        });
    }
};

const nextStatusHandler = () => {
    currentStatus.value =
        currentStatus.value + 1 > 3 ? 3 : currentStatus.value + 1;
    changeStatusHandler(currentStatus.value);
};

const changeStatusHandler = (status) => {
    isSending.value = true;
    currentStatus.value = status;
    changeBillStatus("/admin/dashboard/change-bill-status", "PUT", {
        status: status,
        billId: props.order.id
    })
        .then(() => {
            setStatus();
            Toast.success(
                trans("order_status_changed_successfully"),
                true,
                5000
            );
        })
        .catch((error) => {
            setStatus();
            console.error(error);
        })
        .finally(() => {
            isSending.value = false;
        });
};

watch(
    () => props.order,
    () => {
        currentStatus.value = props.order.status;
        setStatus();
    }
);

onMounted(() => setStatus());
</script>

<template>
    <div :class="mergedClasses">
        <bill-status-cancel-modal
            v-if="showModal"
            :change-bill-status="changeStatusHandler"
            :hide-modal="hideModalHandler"
        />
        <div class="flex w-full justify-between justify-self-start">
            <h3 class="text-xl font-bold">{{ $t("order_status") }}</h3>
            <last-time-edited :updated-at="distanceFormat" />
        </div>
        <div
            class="w-full h-fit mt-4 grid md:grid-cols-12 md:grid-rows-1 md:auto-cols-auto grid-cols-2 grid-rows-1 gap-4 px-4 py-4 items-center"
        >
            <change-status-button
                :on-click="showModalHandler"
                :title="trans('cancel_order')"
                class="bg-red-700 group-hover:bg-red-800 dark:bg-red-600 dark:group-hover:bg-red-700"
            >
                <IconX class="h-10 w-10" />
            </change-status-button>
            <div
                class="relative md:col-span-8 row-span-1 col-span-2 flex h-fit w-full items-center justify-between gap-3"
            >
                <point-element
                    :element-status="elementsStatus[BillStatus.New].pointClass"
                >
                    {{ $t("new") }}
                </point-element>
                <status-bar-element
                    :element-status="
                        elementsStatus[BillStatus.Accepted].statusBar
                    "
                />
                <point-element
                    :element-status="
                        elementsStatus[BillStatus.Accepted].pointClass
                    "
                >
                    {{ $t("accepted") }}
                </point-element>
                <status-bar-element
                    :element-status="elementsStatus[BillStatus.Ready].statusBar"
                    class="left-1/3"
                />
                <point-element
                    :element-status="
                        elementsStatus[BillStatus.Ready].pointClass
                    "
                >
                    {{ $t("ready") }}
                </point-element>
                <status-bar-element
                    :element-status="
                        elementsStatus[BillStatus.Released].statusBar
                    "
                    class="left-2/3"
                />
                <point-element
                    :element-status="
                        elementsStatus[BillStatus.Released].pointClass
                    "
                >
                    {{ $t("released") }}
                </point-element>
            </div>
            <change-status-button
                v-if="!isSending"
                v-show="!isStatusReleased"
                :on-click="nextStatusHandler"
                :title="nextStatusText"
                class="bg-green-900 group-hover:bg-green-800 dark:bg-green-700 dark:group-hover:bg-green-800"
            >
                <IconChevronRight class="h-10 w-10 translate-x-[1px]" />
            </change-status-button>
            <div
                v-else
                class="group max-md:row-start-2 max-md:row-end-3 col-span-1 md:col-span-2 flex cursor-pointer flex-col items-center justify-center gap-2"
            >
                <div class="loader"></div>
            </div>
        </div>
    </div>
</template>
