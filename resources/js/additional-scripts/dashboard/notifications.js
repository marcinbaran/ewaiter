class NotificationsInstance {
    isNotificationBefore = false;
    isNotificationAfter = false;

    instance = null;
    list = null;
    upButton = null;
    downButton = null;

    static instances = [];

    constructor(HTMLInstance) {
        this.#getChildElements(HTMLInstance);
        this.#addButtonsListeners();
    }

    #getChildElements(HTMLInstance) {
        this.instance = HTMLInstance;
        this.list = HTMLInstance.querySelector(
            ".dashboard-notifications--list",
        );
        this.upButton = HTMLInstance.querySelector(
            ".dashboard-notifications--buttons > .scroll-up",
        );
        this.downButton = HTMLInstance.querySelector(
            ".dashboard-notifications--buttons > .scroll-down",
        );
    }

    #moveNotifications = async (direction) => {
        if (direction === "up") {
            this.list.classList.add("transition-transform", "translate-y-24");
            await setTimeout(
                () => this.list.classList.remove("transition-transform"),
                150,
            );
        } else if (direction === "down") {
            this.list.classList.add("transition-transform", "-translate-y-24");
            await setTimeout(
                () => this.list.classList.remove("transition-transform"),
                150,
            );
        }
    };

    #addButtonsListeners() {
        this.upButton.addEventListener("click", () => {
            if (this.isNotificationBefore) {
                this.#moveNotifications("up").then(() => {
                    window.livewire.emit("scrollUpDashboardNotifications");
                });
            }
        });

        this.downButton.addEventListener("click", () => {
            if (this.isNotificationAfter) {
                this.#moveNotifications("down").then(() => {
                    window.livewire.emit("scrollDownDashboardNotifications");
                });
            }
        });
    }

    static initInstances() {
        this.instances.length > 0 && (this.instances.length = 0);
        const notifications = [
            ...document.getElementsByClassName("dashboard-notifications"),
        ];
        notifications.forEach((notification) => {
            const notificationInstance = new NotificationsInstance(
                notification,
            );
            this.instances.push(notificationInstance);
        });
    }
}

window.addEventListener("refreshedDashboardNotifications", () => {
    NotificationsInstance.instances.forEach((instance) => {
        instance.list.classList.remove("translate-y-24", "-translate-y-24");
    });
});

window.addEventListener("missingDashboardNotificationBefore", () => {
    NotificationsInstance.instances.forEach((instance) => {
        instance.isNotificationBefore = false;
    });
});

window.addEventListener("missingDashboardNotificationAfter", () => {
    NotificationsInstance.instances.forEach((instance) => {
        instance.isNotificationAfter = false;
    });
});

window.addEventListener("hasDashboardNotificationBefore", () => {
    NotificationsInstance.instances.forEach((instance) => {
        instance.isNotificationBefore = true;
    });
});

window.addEventListener("hasDashboardNotificationAfter", () => {
    NotificationsInstance.instances.forEach((instance) => {
        instance.isNotificationAfter = true;
    });
});

window.addEventListener("DOMContentLoaded", () => {
    NotificationsInstance.initInstances();
    if (NotificationsInstance.instances.length > 0) {
        window.livewire.emit("refreshDashboardNotifications");
    }
});
