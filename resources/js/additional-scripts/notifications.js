import Toast from "./toast";

function checkIfScrollHasReachedEnd() {
    const notificationDropdown = document.querySelector(
        "#notification-dropdown",
    );
    const scrollPosition =
        notificationDropdown.scrollTop + notificationDropdown.offsetHeight;

    if (scrollPosition == notificationDropdown.scrollHeight) {
        notificationDropdown.scrollTop =
            notificationDropdown.scrollHeight -
            notificationDropdown.offsetHeight -
            1;
        if (hasMorePages) {
            window.livewire.emit("loadMoreNotifications");
        }
    }
}

const notificationsDropdown = document.getElementById("notification-dropdown");

if (notificationsDropdown) {
    let hasMorePages = true;

    setInterval(() => {
        window.livewire.emit("reloadNotificationComponent");
    }, 15000);

    notificationsDropdown.addEventListener("scroll", () => {
        checkIfScrollHasReachedEnd();
    });

    window.addEventListener("notifications", (e) => {
        hasMorePages = e.detail.hasMore;
        checkIfScrollHasReachedEnd();
    });
}

// Push notifications
const pushNotificationButton = document.getElementById(
    "push-notification-button",
);

const requestPushPermission = async () => {
    const permission = await window.Notification.requestPermission();

    if (permission !== "granted") {
        const message = pushNotificationButton.dataset.toastDanger;
        Toast.danger(message);
    } else {
        const message = pushNotificationButton.dataset.toastSuccess;
        Toast.success(message);
        pushNotificationButton.classList.remove("flex");
        pushNotificationButton.classList.add("hidden");
    }
};

const checkIfPushNotificationsGranted = async () => {
    const permission = await window.Notification.requestPermission();
    if (permission !== "granted") {
        pushNotificationButton.classList.remove("hidden");
        pushNotificationButton.classList.add("flex");
    } else {
        pushNotificationButton.classList.remove("flex");
        pushNotificationButton.classList.add("hidden");
    }
};

if (pushNotificationButton) {
    pushNotificationButton.addEventListener("click", requestPushPermission);
    checkIfPushNotificationsGranted();
}
