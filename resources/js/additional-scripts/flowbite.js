import { initFlowbite, Drawer, Modal } from "flowbite";

// initialize flowbite
initFlowbite();
window.addEventListener("initFlowbite", () => {
    initFlowbite();
});

const initializeAsideDrawer = () => {
    const targetElement = document.getElementById("default-sidebar");
    const buttonElement = document.getElementById("toggleSidebarMobile");
    const options = {
        placement: "left",
        backdrop: true,
        bodyScrolling: false,
        edge: false,
        edgeOffset: "",
        backdropClasses:
            "bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-30",
    };
    const drawer = new Drawer(targetElement, options);
    buttonElement.addEventListener("click", () => {
        drawer.toggle();
    });
};

const initializeNewAdditionModal = () => {
    const targetElement = document.getElementById("newAdditionModal");
    const showModalButton = document.getElementById("newAdditionButton");
    const hideModalElements = document.querySelectorAll(
        ".newAdditionModalClose",
    );
    const options = {
        backdrop: true,
        bodyScrolling: false,
        edge: false,
        edgeOffset: "",
        backdropClasses:
            "bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-50",
    };
    const modal = new Modal(targetElement, options);
    showModalButton.addEventListener("click", () => {
        modal.show();
    });
    hideModalElements.forEach((element) => {
        element.addEventListener("click", (e) => {
            modal.hide();
        });
    });
};

const sidebar = document.getElementById("default-sidebar");
if (sidebar) {
    initializeAsideDrawer();
}

const newAdditionModal = document.getElementById("newAdditionModal");
if (newAdditionModal) {
    initializeNewAdditionModal();
}
