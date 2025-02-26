import { Dismiss } from "flowbite";

class Toast extends HTMLElement {
    constructor() {
        super();
        this.message = "No message";
        this.type = "success";
        this.dismissable = "true";
        this.dismissTimeout = 10000;
    }
    connectedCallback() {
        this.message = this.getAttribute("message") ?? this.message;
        this.type = this.getAttribute("type") ?? this.type;
        this.dismissable =
            (this.getAttribute("dismissable") ?? this.dismissable) === "true"
                ? true
                : false;
        this.dismissTimeout = parseInt(
            this.getAttribute("dismissTimeout") ?? this.dismissTimeout,
        );
        this.id = this.generateRandomId(this.type);

        const toast = this.createMainElement(this.id);
        const icon = this.createIcon(this.type);
        const message = this.createMessage(this.message);
        const progressBar = this.createAnimation(this.dismissTimeout);

        toast.appendChild(icon);
        toast.appendChild(message);
        toast.appendChild(this.createDismissButton(this.id));
        if (this.dismissable) {
            toast.appendChild(progressBar);
        }

        this.appendChild(toast);
        this.addDismissTimeout(this.dismissable, this.dismissTimeout);
    }
    generateRandomId(type) {
        const timestamp = new Date().getTime();
        const randomNumber = Math.floor(Math.random() * 1000000);
        const id = `toast-${type}-${timestamp}${randomNumber}`;
        return id;
    }
    createMainElement(id) {
        const mainElement = document.createElement("div");
        mainElement.classList.add("toast--main");
        mainElement.setAttribute("role", "alert");
        mainElement.setAttribute("id", id);
        return mainElement;
    }
    createIcon(type) {
        const iconElement = document.createElement("div");
        iconElement.classList.add("toast--icon");
        if (type === "success") {
            iconElement.classList.add(
                "text-green-500",
                "bg-green-100",
                "dark:bg-green-800",
                "dark:text-green-200",
            );
            iconElement.innerHTML = `<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>`;
        } else if (type === "danger") {
            iconElement.classList.add(
                "text-red-500",
                "bg-red-100",
                "dark:bg-red-800",
                "dark:text-red-200",
            );
            iconElement.innerHTML = `<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/></svg>`;
        } else if (type === "warning") {
        } else {
        }
        return iconElement;
    }
    createMessage(message) {
        const messageElement = document.createElement("div");
        messageElement.classList.add("toast--text");
        messageElement.innerHTML = message;
        return messageElement;
    }
    createDismissButton(id) {
        const dismissButton = document.createElement("button");
        dismissButton.classList.add("toast-dismiss");
        dismissButton.setAttribute("data-dismiss-target", `#${id}`);
        dismissButton.setAttribute("aria-label", "Close");
        dismissButton.innerHTML = `<span class="sr-only">Close</span><svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>`;
        const dismissOptions = {
            transition: "transition-opacity",
            duration: 1000,
            timing: "ease-out",
        };
        this.dismiss = new Dismiss(this, dismissButton, dismissOptions);
        return dismissButton;
    }
    createAnimation(timeout) {
        const progressBar = document.createElement("div");
        progressBar.classList.add("toast--progress-bar");
        progressBar.style.animationDuration = `${timeout}ms`;
        return progressBar;
    }
    addDismissTimeout(dismissable, dismissTimeout) {
        if (dismissable) {
            setTimeout(() => {
                this.dismiss.hide();
            }, dismissTimeout);
        }
    }
}

window.customElements.define("new-toast", Toast);

const createToastSpace = () => {
    const toastSpace = document.createElement("div");
    toastSpace.id = "toast-space";
    document.body.appendChild(toastSpace);
};

const createToast = (message, type, dismissable, dismissTimeout) => {
    let toastSpace = document.getElementById("toast-space");
    if (!toastSpace) {
        createToastSpace();
        toastSpace = document.getElementById("toast-space");
    }

    const toast = document.createElement("new-toast");
    message && toast.setAttribute("message", message);
    type && toast.setAttribute("type", type);
    dismissable && toast.setAttribute("dismissable", dismissable);
    dismissTimeout && toast.setAttribute("dismissTimeout", dismissTimeout);
    toastSpace.appendChild(toast);
};

const toast = {
    success: (message, dismissable, dismissTimeout) => {
        createToast(message, "success", dismissable, dismissTimeout);
    },
    danger: (message, dismissable, dismissTimeout) => {
        createToast(message, "danger", dismissable, dismissTimeout);
    },
    space: () => {
        createToastSpace();
    },
};

export default toast;
