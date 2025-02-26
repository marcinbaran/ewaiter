import PerfectScrollbar from "perfect-scrollbar";
import "perfect-scrollbar/css/perfect-scrollbar.css";

import { getPath } from "../helpers";

const psElements = [
    { query: "#sidebar-navigation" },
    { query: "#main-content" },
    { query: "#searchModal > div" },
    { query: "#settings-menu" },
    { query: ".hero-widget--tabs" },
    { query: "#notification-dropdown" },
    {
        query: "#foodDatatable",
        wheelPropagation: true,
    },
];

const disabledPaths = ["/admin/dashboard/orders-fullscreen"];

const perfectScrollbarElements = [];

const addScrollbarToElement = (el, wheelPropagation = true) => {
    el.classList.add("relative");
    el.classList.add("overflow-hidden");
    const elPS = new PerfectScrollbar(el, {
        wheelPropagation: wheelPropagation,
    });
    perfectScrollbarElements.push(elPS);
    return elPS;
};

export const initPS = () => {
    if ("ontouchstart" in document.documentElement) {
        return;
    }
    if (disabledPaths.length > 0) {
        disabledPaths.forEach((path) => {
            if (getPath() === path) {
                return path;
            }
        });
    }

    if (perfectScrollbarElements.length > 0) {
        perfectScrollbarElements.forEach((elPS) => elPS.destroy());
    }

    psElements.forEach((psElement) => {
        const elements = document.querySelectorAll(psElement.query);
        elements.forEach((element) => {
            element &&
                addScrollbarToElement(element, psElement.wheelPropagation);
        });
    });

    document
        .querySelectorAll('div[id^="datatable"] > div')
        .forEach((element) => {
            if (element.classList.contains("overflow-x-scroll")) {
                element.classList.remove("overflow-x-scroll");
            }
            if (element.classList.contains("overflow-y-scroll")) {
                element.classList.remove("overflow-y-scroll");
            }
            if (element.querySelector("table")) {
                addScrollbarToElement(element);
            }
        });

    document.querySelectorAll("#foodDatatable").forEach((element) => {
        if (element.classList.contains("overflow-x-scroll")) {
            element.classList.remove("overflow-x-scroll");
        }
        if (element.classList.contains("overflow-y-scroll")) {
            element.classList.remove("overflow-y-scroll");
        }
        if (element.querySelector("table")) {
            addScrollbarToElement(element);
        }
    });
};

window.addEventListener("rerenderScrollBar", initPS);

initPS();

const observer = new MutationObserver((mutations, observer) => initPS());

document
    .querySelectorAll('div[id^="datatable"] .before-tools')
    .forEach((element) => {
        observer.observe(element, {
            childList: true,
        });
    });
