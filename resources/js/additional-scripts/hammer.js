const newOrdersEl = document.querySelector("#new-orders-element");
const actualOrdersEl = document.querySelector("#actual-orders-element");

if (newOrdersEl) {
    const newOrders = new Hammer.Manager(newOrdersEl);
    const SwipeNewOrders = new Hammer.Swipe();
    newOrders.add(SwipeNewOrders);
    newOrders.on("swipeleft", function (e) {
        window.livewire.emit("newOrdersSwipedLeft");
    });

    newOrders.on("swiperight", function (e) {
        window.livewire.emit("newOrdersSwipedRight");
    });
}

if (actualOrdersEl) {
    const actualOrders = new Hammer.Manager(actualOrdersEl);
    const SwipeActualOrders = new Hammer.Swipe();
    actualOrders.add(SwipeActualOrders);
    actualOrders.on("swipeleft", function (e) {
        window.livewire.emit("actualOrdersSwipedLeft");
    });

    actualOrders.on("swiperight", function (e) {
        window.livewire.emit("actualOrdersSwipedRight");
    });
}
