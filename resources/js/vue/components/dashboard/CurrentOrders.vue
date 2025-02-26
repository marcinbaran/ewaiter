<script setup>
import { ref, onMounted, computed, watch, watchEffect } from "vue";
import { sendRequest } from "../../../helpers.js";
import { useSwipe } from "@vueuse/core";
import config from "../../../../config/config.js";

import exampleOrder from "./orders-partials/exampleOrders.js";
import Toast from "../../../additional-scripts/toast";

import Pagination from "./orders-partials/Pagination.vue";
import ClientDetails from "./orders-partials/ClientDetails.vue";
import OrderDetails from "./orders-partials/OrderDetails.vue";
import PayTimeInfo from "./orders-partials/PayTimeInfo.vue";
import ZeroData from "./orders-partials/ZeroData.vue";
import BillStatus from "./orders-partials/BillStatus.vue";
import Spinner from "../ui/Spinner.vue";
import HorizontalDivider from "./orders-partials/HorizontalDivider.vue";
import PriceDetailsModal from "./orders-partials/PriceDetailsModal.vue";
import WaitTimeUpdateModal from "./orders-partials/WaitTimeUpdateModal.vue";
import { addMinutes, format } from "date-fns";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
    sectionId: {
        type: String,
        required: true,
    },
    notificationCountHandler: {
        type: Function,
        required: true,
    },
    class: {
        type: String,
        default: "",
    },
});

const orderElementRef = ref(null);
const { isSwiping, direction } = useSwipe(orderElementRef);

const orders = ref([]);
const showPriceDetailsModal = ref(false);
const showWaitTimeUpdateModal = ref(false);
const displayedOrder = ref(exampleOrder);
const ordersLenght = ref(0);
const isLoading = ref(true);
const currentPageNumber = ref(1);

const updatingWaitTime = ref(false);
const errorWhileUpdatingWaitTime = ref(false);

const swiping = ref(isSwiping);
const directionOfSwipe = ref(direction);

const mergedClasses = computed(
    () =>
        `new-orders w-full h-full flex flex-col gap-6 overflow-hidden relative ${props.class}`,
);

const changeCurrentlyDisplayedOrder = (orderNumber) => {
    currentPageNumber.value = orderNumber;
    displayedOrder.value = orders.value[orderNumber - 1];
};

const getActualOrders = () => sendRequest("/admin/dashboard/actual-orders");

const refreshOrders = async () => {
    try {
        const lengthOfOldOrders = orders.value.length;
        orders.value = [...(await getActualOrders()).results];
        ordersLenght.value = orders.value.length;
        if (ordersLenght.value == 1 || currentPageNumber.value == 1) {
            displayedOrder.value = orders.value[0];
        } else if (currentPageNumber.value > ordersLenght.value) {
            displayedOrder.value = orders.value[ordersLenght.value - 1];
        } else if (lengthOfOldOrders > ordersLenght.value) {
            displayedOrder.value = orders.value[currentPageNumber.value - 1];
        }
        props.notificationCountHandler(props.sectionId, ordersLenght.value);
    } catch (error) {
        console.log(error);
    }
};

const isThereAnyOrders = computed(() => {
    return ordersLenght.value > 0;
});

const displayedOrderClientDetails = computed(() => {
    const clientDetails = {
        name:
            displayedOrder.value.address?.name ??
            displayedOrder.value.user?.first_name,
        email:
            displayedOrder.value.address?.email ??
            displayedOrder.value.user?.email,
        phone:
            displayedOrder.value.address?.phone ??
            displayedOrder.value.user?.phone,
        street: displayedOrder.value.address?.street,
        buildingNumber: displayedOrder.value.address?.building_number,
        flatNumber: displayedOrder.value.address?.house_number,
        postcode: displayedOrder.value.address?.postcode,
        city: displayedOrder.value.address?.city,
        companyName: displayedOrder.value.address?.company_name,
    };
    return clientDetails;
});

const hidePriceDetailsModal = () => {
    showPriceDetailsModal.value = false;
};

const showPriceDetailsModalHandler = () => {
    showPriceDetailsModal.value = true;
};

const hideWaitTimeUpdateModal = () => {
    showWaitTimeUpdateModal.value = false;
};

const showWaitTimeUpdateModalHandler = () => {
    showWaitTimeUpdateModal.value = true;
};

const updateWaitTimeHandler = (timeWait) => {
    updatingWaitTime.value = true;
    updateWaitTime(timeWait)
        .then(() => {
            updatingWaitTime.value = false;
            displayedOrder.value.timeWait = format(
                addMinutes(new Date(displayedOrder.value.timeWait), timeWait),
                config.DATE_TIME_FORMAT_FNS,
            );
            hideWaitTimeUpdateModal();
            Toast.success(trans("wait_time_updated"), true, 5000);
        })
        .catch(() => {
            updatingWaitTime.value = false;
            errorWhileUpdatingWaitTime.value = true;
        });
};
const updateWaitTime = async (timeWait) =>
    sendRequest(
        `/admin/dashboard/update-wait-time/${displayedOrder.value.id}`,
        "PUT",
        { timeWait },
    );

watch(
    () => swiping.value,
    (newVal) => {
        if (newVal) {
            if (directionOfSwipe.value === "left") {
                if (currentPageNumber.value < ordersLenght.value) {
                    changeCurrentlyDisplayedOrder(currentPageNumber.value + 1);
                }
            } else if (directionOfSwipe.value === "right") {
                if (currentPageNumber.value > 1) {
                    changeCurrentlyDisplayedOrder(currentPageNumber.value - 1);
                }
            }
        }
    },
);

onMounted(async () => {
    try {
        orders.value = [...(await getActualOrders()).results];
        ordersLenght.value = orders.value.length;
        displayedOrder.value = orders.value[0];
        setInterval(async () => {
            await refreshOrders();
        }, 5000);
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div :class="mergedClasses">
        <div
            ref="orderElementRef"
            v-if="isThereAnyOrders"
            class="actual-orders w-full h-full flex flex-col gap-3 p-3"
        >
            <Pagination
                :changeOrder="changeCurrentlyDisplayedOrder"
                :currentPageNumber="currentPageNumber"
                :numberOfPages="ordersLenght"
                :order-id="displayedOrder.id"
                :order-created-at="displayedOrder.createdAt"
            />
            <div
                class="actual-orders__content w-full h-full overflow-hidden flex-1"
            >
                <perfect-scrollbar
                    class="sm:grid-rows-order-horizontal grid-rows-order-vertical grid h-full auto-rows-min grid-cols-1 gap-4 sm:grid-cols-2"
                >
                    <ClientDetails
                        :company-name="displayedOrderClientDetails.companyName"
                        :first-name="displayedOrderClientDetails.name"
                        :email="displayedOrderClientDetails.email"
                        :phone="displayedOrderClientDetails.phone"
                        :street="displayedOrderClientDetails.street"
                        :building-number="
                            displayedOrderClientDetails.buildingNumber
                        "
                        :flat-number="displayedOrderClientDetails.flatNumber"
                        :postcode="displayedOrderClientDetails.postcode"
                        :city="displayedOrderClientDetails.city"
                        :comment="displayedOrder.comment"
                        :is-scrollable="false"
                        class="border-gray-300 pr-4 dark:border-gray-700 sm:col-start-1 sm:col-end-2 sm:row-start-1 sm:row-end-2 sm:border-r"
                    />
                    <HorizontalDivider class="sm:hidden" />
                    <OrderDetails
                        :orders="displayedOrder.orders"
                        :payment-type="displayedOrder.paidType"
                        :delivery-type="displayedOrder.deliverySettingsType"
                        :delivery-room-number="displayedOrder.roomDelivery"
                        :delivery-table-number="displayedOrder.tableNumber"
                        :is-scrollable="false"
                        class="sm:col-start-2 sm:col-end-3 sm:row-start-1 sm:row-end-2"
                    />
                    <HorizontalDivider class="sm:hidden" />
                    <HorizontalDivider
                        class="col-span-2 row-start-2 row-end-3 hidden sm:block"
                    />
                    <PayTimeInfo
                        :is-paid="displayedOrder.paid === 1"
                        :price="displayedOrder.priceToPay.toFixed(2)"
                        :points="displayedOrder.points"
                        :wait-time="displayedOrder.timeWait"
                        :show-price-details-modal-handler="
                            showPriceDetailsModalHandler
                        "
                        :show-wait-time-update-modal="
                            showWaitTimeUpdateModalHandler
                        "
                        class="col-span-2"
                    />
                    <HorizontalDivider class="sm:hidden" />
                    <HorizontalDivider
                        class="col-span-2 row-start-4 row-end-5 hidden sm:block"
                    />
                    <BillStatus
                        :order="displayedOrder"
                        :status="displayedOrder.status"
                        :refreshOrders="refreshOrders"
                        class="col-span-2"
                    />
                </perfect-scrollbar>
            </div>
        </div>
        <div v-else class="w-full h-full">
            <ZeroData :refreshOrders="refreshOrders" />
        </div>
        <div
            class="absolute inset-0 flex items-center justify-center backdrop-blur transition-opacity"
            v-if="isLoading"
            ref="spinnerRef"
        >
            <Spinner />
        </div>
        <PriceDetailsModal
            v-if="showPriceDetailsModal"
            :dish-price="displayedOrder.prices?.dishes ?? '0.00'"
            :delivery-price="displayedOrder.prices?.delivery ?? '0.00'"
            :package-price="displayedOrder.prices?.packages ?? '0.00'"
            :service-charge="displayedOrder.prices?.serviceCharge ?? '0.00'"
            :discount-value="displayedOrder.prices?.discount ?? '0.00'"
            :points-value="displayedOrder.prices?.points ?? '0.00'"
            :on-close="hidePriceDetailsModal"
            :is-paid="displayedOrder.paid === 1"
        />
        <WaitTimeUpdateModal
            v-if="showWaitTimeUpdateModal"
            :close-wait-time-update-modal="hideWaitTimeUpdateModal"
            :current-wait-time="displayedOrder.timeWait"
            :update-wait-time="updateWaitTimeHandler"
            :is-updating-wait-time="updatingWaitTime"
            :has-error="errorWhileUpdatingWaitTime"
        />
    </div>
</template>
