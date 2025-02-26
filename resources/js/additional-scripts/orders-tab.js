const newOrders = document.querySelector("#newOrders");
const actualOrders = document.querySelector("#actualOrders");
const newOrdersTab = document.querySelector("#newOrdersTab");
const actualOrdersTab = document.querySelector("#actualOrdersTab");

if (newOrders && actualOrders && newOrdersTab && actualOrdersTab) {
    window.livewire.emit("getNewNumberOfOrders");
    window.livewire.emit("getActualNumberOfOrders");
    const newOrdersNumberOfOrders = document.createElement("span");
    const actualOrdersNumberOfOrders = document.createElement("span");
    newOrdersTab.appendChild(newOrdersNumberOfOrders);
    actualOrdersTab.appendChild(actualOrdersNumberOfOrders);

    window.addEventListener("getNewNumberOfOrders", (e) => {
        if (e.detail.numberOfOrders > 0) {
            newOrdersNumberOfOrders.innerHTML = ` (${e.detail.numberOfOrders})`;
        }
    });
    window.addEventListener("getActualNumberOfOrders", (e) => {
        if (e.detail.numberOfOrders > 0) {
            actualOrdersNumberOfOrders.innerHTML = ` (${e.detail.numberOfOrders})`;
        }
    });
}
