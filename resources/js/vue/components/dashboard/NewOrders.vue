<template>
    <div :class="mergedClasses">
        <div
            v-if="isThereAnyOrders"
            ref="orderElementRef"
            class="new-orders w-full h-full flex flex-col gap-3 p-3"
        >
            <Pagination
                :changeOrder="changeCurrentlyDisplayedOrder"
                :currentPageNumber="currentPageNumber"
                :newOrderShowed="newOrderShowed"
                :numberOfPages="ordersLenght"
                :order-created-at="displayedOrder.createdAt"
                :order-id="displayedOrder.id"
            />
            <div
                class="new-orders__content w-full h-full overflow-hidden flex-1"
            >
                <div
                    class="sm:grid-rows-order-horizontal grid-rows-order-vertical grid h-full auto-rows-min grid-cols-1 gap-4 sm:grid-cols-2"
                >
                    <ClientDetails
                        :building-number="
                            displayedOrderClientDetails.buildingNumber
                        "
                        :city="displayedOrderClientDetails.city"
                        :comment="displayedOrder.comment"
                        :company-name="displayedOrderClientDetails.companyName"
                        :email="displayedOrderClientDetails.email"
                        :first-name="displayedOrderClientDetails.name"
                        :flat-number="displayedOrderClientDetails.flatNumber"
                        :is-order-paid="displayedOrder.paid === 1"
                        :order-points="displayedOrder.points"
                        :order-price="displayedOrder.priceToPay.toFixed(2)"
                        :phone="displayedOrderClientDetails.phone"
                        :postcode="displayedOrderClientDetails.postcode"
                        :show-price-details-modal-handler="
                            showPriceDetailsModalHandler
                        "
                        :street="displayedOrderClientDetails.street"
                        class="border-gray-300 pr-4 dark:border-gray-700 sm:col-start-1 sm:col-end-2 sm:row-start-1 sm:row-end-2 sm:border-r"
                    />
                    <HorizontalDivider class="sm:hidden" />
                    <OrderDetails
                        :delivery-room-number="displayedOrder.roomDelivery"
                        :delivery-table-number="displayedOrder.tableNumber"
                        :delivery-type="displayedOrder.deliverySettingsType"
                        :orders="displayedOrder.orders"
                        :payment-type="displayedOrder.paidType"
                        class="sm:col-start-2 sm:col-end-3 sm:row-start-1 sm:row-end-2"
                    />
                    <HorizontalDivider class="sm:hidden" />
                    <OrderWaitTime
                        :isNewOrder="true"
                        :isPageChanging="isPageChanging"
                        :order="displayedOrder"
                        :showError="showError"
                        :updateWaitTime="updateWaitTime"
                    />
                    <HorizontalDivider class="sm:hidden" />
                    <div
                        class="flex justify-end items-center gap-2 text-gray-50 dark:text-gray-900"
                    >
                        <Button
                            :disabled="areButtonsDisabled"
                            :on-click="showModalHandler"
                            color="danger"
                        >
                            {{ $t("cancel_order") }}
                        </Button>
                        <Button
                            :disabled="areButtonsDisabled"
                            :on-click="acceptOrderHandler"
                            color="success"
                        >
                            {{ $t("accept_order") }}
                        </Button>
                    </div>
                    <HorizontalDivider
                        class="col-span-2 row-start-2 row-end-3 hidden sm:block"
                    />
                </div>
            </div>
            <div
                v-if="showModal"
                class="flex w-full flex-col gap-4 justify-self-end"
            >
                <div
                    class="absolute left-1/2 top-1/2 h-full w-full -translate-x-1/2 -translate-y-1/2 rounded-lg backdrop-blur"
                    @click="showModalHandler"
                ></div>
                <div
                    class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center gap-4 rounded-lg border border-gray-300 bg-gray-200 p-4 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50"
                >
                    <p class="text-center">
                        {{ $t("are_you_sure_that_you_want_to_cancel_order") }}
                    </p>
                    <div class="flex items-center justify-center gap-4">
                        <Button :on-click="showModalHandler" color="danger">
                            {{ $t("no") }}
                        </Button>
                        <Button :on-click="cancelOrderHandler" color="success">
                            {{ $t("yes") }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="w-full h-full">
            <ZeroData :refreshOrders="refreshOrders" />
        </div>
        <div
            v-if="isLoading"
            ref="spinnerRef"
            class="absolute inset-0 flex items-center justify-center backdrop-blur transition-opacity"
        >
            <Spinner />
        </div>
        <PriceDetailsModal
            v-if="showPriceDetailsModal"
            :delivery-price="displayedOrder.prices?.delivery ?? '0.00'"
            :discount-value="displayedOrder.prices?.discount ?? '0.00'"
            :dish-price="displayedOrder.prices?.dishes ?? '0.00'"
            :is-paid="displayedOrder.paid === 1"
            :on-close="hidePriceDetailsModal"
            :package-price="displayedOrder.prices?.packages ?? '0.00'"
            :points-value="displayedOrder.prices?.points ?? '0.00'"
            :service-charge="displayedOrder.prices?.serviceCharge ?? '0.00'"
        />
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { useSwipe } from "@vueuse/core";
import { trans } from "laravel-vue-i18n";

import { sendRequest } from "../../../helpers";
import Toast from "../../../additional-scripts/toast";

import exampleOrder from "./orders-partials/exampleOrders.js";
import Pagination from "./orders-partials/Pagination.vue";
import ClientDetails from "./orders-partials/ClientDetails.vue";
import OrderDetails from "./orders-partials/OrderDetails.vue";
import OrderWaitTime from "./orders-partials/OrderWaitTime.vue";
import HorizontalDivider from "./orders-partials/HorizontalDivider.vue";
import ZeroData from "./orders-partials/ZeroData.vue";
import Spinner from "../ui/Spinner.vue";
import Button from "./orders-partials/Button.vue";
import PriceDetailsModal from "./orders-partials/PriceDetailsModal.vue";

const props = defineProps({
    sectionId: {
        type: String,
        required: true
    },
    notificationCountHandler: {
        type: Function,
        required: true
    },
    class: {
        type: String,
        default: ""
    }
});

const showError = ref(false);
const showModal = ref(false);
const showPriceDetailsModal = ref(false);
const orders = ref([]);
const displayedOrder = ref(exampleOrder);
const isLoading = ref(true);
const waitTime = ref(0);
const newOrderIds = ref([]);
const currentPageNumber = ref(1);
const isPageChanging = ref(false);
const areButtonsDisabled = ref(false);

const orderElementRef = ref(null);
const { isSwiping, direction } = useSwipe(orderElementRef);

const swiping = ref(isSwiping);
const directionOfSwipe = ref(direction);

const newOrderShowed = computed(() =>
    newOrderIds.value.length === 0 ? false : true
);

const ordersLenght = computed(() => orders.value.length);

const mergedClasses = computed(
    () =>
        `new-orders w-full h-full flex flex-col gap-6 overflow-hidden relative ${props.class}`
);

watch(displayedOrder.value, () => {
    if (newOrderIds.value.length > 0) {
        if (newOrderIds.value.includes(displayedOrder.value.id)) {
            newOrderIds.value = newOrderIds.value.filter(
                (orderId) => orderId != displayedOrder.value.id
            );
        }
    }
});

const isThereAnyOrders = computed(() => {
    return ordersLenght.value > 0;
});

const displayedOrderClientDetails = computed(() => {
    const clientDetails = {
        name: displayedOrder.value.user?.first_name,
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
        companyName: displayedOrder.value.address?.company_name
    };
    return clientDetails;
});

const showModalHandler = () => {
    showModal.value = !showModal.value;
};

const getNewOrders = () => sendRequest("/admin/dashboard/new-orders");

const acceptOrder = async () =>
    sendRequest("/admin/dashboard/accept-order", "PUT", displayedOrder.value);

const cancelOrder = async (orderId) =>
    sendRequest(`/admin/dashboard/cancel-order/${orderId}`, "PUT");

const getNewOrderIds = (newOrders, oldOrders) => {
    const oldOrdersIds = oldOrders.map((order) => order.id);
    const newOrdersIds = newOrders.map((order) => order.id);
    const newOrderIds = newOrdersIds.filter(
        (orderId) => !oldOrdersIds.includes(orderId)
    );
    return newOrderIds;
};

const acceptOrderHandler = async () => {
    try {
        console.log(waitTime.value);
        if (
            waitTime.value === 0 ||
            waitTime.value === null ||
            waitTime.value === undefined ||
            waitTime.value === ""
        ) {
            showError.value = true;
            return;
        } else {
            areButtonsDisabled.value = true;
            await acceptOrder();
            orders.value = orders.value.filter(
                (order) => order.id !== displayedOrder.value.id
            );
            isPageChanging.value = true;
            setTimeout(() => {
                isPageChanging.value = false;
            }, 500);
            displayedOrder.value =
                ordersLenght.value > 0 ? orders.value[0] : {};
            Toast.success(trans("order_accepted_successfully"), true, 5000);
            areButtonsDisabled.value = false;
        }
    } catch (error) {
        console.log(error);
    }
};

const cancelOrderHandler = async () => {
    const orderId = displayedOrder.value.id;
    try {
        areButtonsDisabled.value = true;
        await cancelOrder(orderId);
        orders.value = orders.value.filter((order) => order.id !== orderId);
        showModal.value = false;
        displayedOrder.value = ordersLenght.value > 0 ? orders.value[0] : {};
        Toast.success(trans("order_canceled_successfully"), true, 5000);
        areButtonsDisabled.value = false;
    } catch (error) {
        console.log(error);
    }
};

const updateWaitTime = (waitTimeValue) => {
    waitTime.value = waitTimeValue;

    displayedOrder.value.timeWait = waitTime.value;
    if (showError.value) {
        showError.value = false;
    }
};

const changeCurrentlyDisplayedOrder = (orderNumber) => {
    currentPageNumber.value = orderNumber;
    displayedOrder.value = orders.value[orderNumber - 1];
};

const refreshOrders = async () => {
    const oldOrders = [...orders.value];
    const newOrders = [...(await getNewOrders()).results];
    if (oldOrders.length < newOrders.length) {
        newOrderIds.value = getNewOrderIds(newOrders, oldOrders);
    }
    orders.value = newOrders;
    if (oldOrders.length <= 1) {
        displayedOrder.value = orders.value[0];
    }
    props.notificationCountHandler(props.sectionId, newOrders.length);
};

const hidePriceDetailsModal = () => {
    showPriceDetailsModal.value = false;
};

const showPriceDetailsModalHandler = () => {
    showPriceDetailsModal.value = true;
};

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
    }
);

watch(
    () => currentPageNumber.value,
    () => {
        if (!isPageChanging.value) {
            isPageChanging.value = true;
            setTimeout(() => {
                isPageChanging.value = false;
            }, 500);
        }
    }
);

onMounted(async () => {
    try {
        const newOrders = [...(await getNewOrders()).results];
        orders.value = [...newOrders];
        setInterval(async () => {
            refreshOrders();
        }, 5000);
        displayedOrder.value = orders.value[0];
        props.notificationCountHandler(props.sectionId, newOrders.length);
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
});
</script>
