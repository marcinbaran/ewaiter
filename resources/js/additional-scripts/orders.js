import Toast from "./toast.js";

let newOrders = document.querySelector(".new-orders__content");
let actualOrders = document.querySelector(".actual-orders__content");
const newOrdersContainer = document.querySelector(".new-orders");
const actualOrdersContainer = document.querySelector(".actual-orders");
let scrollableArrowActualOrders = document.querySelector(
    ".scrollabe-icon-arrow-actual-orders",
);
let scrollableArrowNewOrders = document.querySelector(
    ".scrollabe-icon-arrow-new-orders",
);
let scrollIndicatorActualOrders = document.querySelector(
    ".scroll-indicator-actual-orders",
);
let scrollIndicatorNewOrders = document.querySelector(
    ".scroll-indicator-new-orders",
);

function checkIfNewOrdersContainerHasScrollBar() {
    if (newOrders) {
        if (
            newOrders.scrollHeight > newOrders.clientHeight &&
            newOrders.scrollTop == 0
        ) {
            return true;
        }
    }
    return false;
}

function checkIfActualOrdersContainerHasScrollBar() {
    if (actualOrders) {
        if (
            actualOrders.scrollHeight > actualOrders.clientHeight &&
            actualOrders.scrollTop == 0
        ) {
            return true;
        }
    }
    return false;
}

function showIndicatorForElement(
    checkIfContentHasScroll,
    indicator,
    element,
    arrowIcon,
) {
    if (element) {
        if (checkIfContentHasScroll()) {
            element.classList.add("scrollable");
            arrowIcon.classList.remove("hidden");
            indicator.classList.remove("hidden");
        }
    }
}

function hideIndicatorForElement(indicator, element, arrowIcon, emit) {
    element.classList.remove("scrollable");
    arrowIcon.classList.add("hidden");
    indicator.classList.add("hidden");
    window.livewire.emit(emit);
}

function reasingElements() {
    newOrders = document.querySelector(".new-orders__content");
    actualOrders = document.querySelector(".actual-orders__content");
    scrollableArrowActualOrders = document.querySelector(
        ".scrollabe-icon-arrow-actual-orders",
    );
    scrollableArrowNewOrders = document.querySelector(
        ".scrollabe-icon-arrow-new-orders",
    );
    scrollIndicatorActualOrders = document.querySelector(
        ".scroll-indicator-actual-orders",
    );
    scrollIndicatorNewOrders = document.querySelector(
        ".scroll-indicator-new-orders",
    );

    addEventListenerForScrollableElements(
        newOrders,
        scrollIndicatorNewOrders,
        scrollableArrowNewOrders,
        "newOrdersScrolled",
    );
    addEventListenerForScrollableElements(
        actualOrders,
        scrollIndicatorActualOrders,
        scrollableArrowActualOrders,
        "actualOrdersScrolled",
    );
}

function addEventListenerForScrollableElements(
    element,
    scrollIndicator,
    arrowIcon,
    emit,
) {
    if (element) {
        element.addEventListener("scroll", function eventHandler() {
            hideIndicatorForElement(scrollIndicator, element, arrowIcon, emit);
            this.removeEventListener("scroll", eventHandler);
        });
    }
    if (scrollIndicator) {
        scrollIndicator.addEventListener("click", function eventHandler() {
            if (element) {
                element.scrollTop = element.scrollHeight;

                hideIndicatorForElement(
                    scrollIndicator,
                    element,
                    arrowIcon,
                    emit,
                );
            }
            this.removeEventListener("click", eventHandler);
        });
    }
}

if (newOrdersContainer && actualOrdersContainer) {
    showIndicatorForElement(
        checkIfActualOrdersContainerHasScrollBar,
        scrollIndicatorActualOrders,
        actualOrders,
        scrollableArrowActualOrders,
    );
    showIndicatorForElement(
        checkIfNewOrdersContainerHasScrollBar,
        scrollIndicatorNewOrders,
        newOrders,
        scrollableArrowNewOrders,
    );

    reasingElements();

    window.addEventListener("checkIfNewOrdersContentIsScrollable", () => {
        reasingElements();
        showIndicatorForElement(
            checkIfNewOrdersContainerHasScrollBar,
            scrollIndicatorNewOrders,
            newOrders,
            scrollableArrowNewOrders,
        );
    });
    window.addEventListener("checkIfActualOrdersContentIsScrollable", () => {
        reasingElements();
        showIndicatorForElement(
            checkIfActualOrdersContainerHasScrollBar,
            scrollIndicatorActualOrders,
            actualOrders,
            scrollableArrowActualOrders,
        );
    });

    setInterval(() => {
        window.livewire.emit("showScrollElement");
    }, 30000);

    window.addEventListener("toastActualOrders", (e) => {
        if (e.detail.hasOwnProperty("message")) {
            Toast.success(e.detail.message);
        }
    });
}
