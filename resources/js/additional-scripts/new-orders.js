import toast from "./toast";

const newOrders = document.getElementById("new-orders-element");

const playSound = () => {
    const newAudio = new Audio("/sounds/new-order-sound.mp3");
    newAudio.play();
};
const playSoundForUnseenOrders = () => {
    const newAudio = new Audio("/sounds/unseen-order-sound.mp3");
    newAudio.play();
};

if (newOrders) {
    document.addEventListener("DOMContentLoaded", () => {
        window.livewire.on("newOrderArrived", () => {
            playSound();
        });
        window.livewire.on("newOrdersUnseen", () => {
            playSoundForUnseenOrders();
        });
        window.livewire.on("newOrderAccepted", () => {
            const message = newOrders.dataset.toastOrderAccepted;
            toast.success(message);
        });
        window.livewire.on("newOrderCancelled", () => {
            const message = newOrders.dataset.toastOrderCancelled;
            toast.success(message);
        });
    });
}
