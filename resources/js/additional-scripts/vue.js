import { createApp } from "vue";
import { i18nVue } from "laravel-vue-i18n";
import PerfectScrollbar from "vue3-perfect-scrollbar";
import "vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css";

import HeroWidget from "../vue/components/dashboard/HeroWidget.vue";
import NotificationWrapper from "../vue/components/notifications/NotificationWrapper.vue";
import PolygonDeliveryRangeConfiguration from "../vue/components/settings/PolygonDeliveryRangeConfiguration.vue";

document.addEventListener("DOMContentLoaded", () => {
    const dashboardApp = createApp({});
    const dashboardAppElement = document.getElementById("dashboard-app");
    const notifications = createApp({});
    const notificationsElement = document.getElementById("notifications");
    const polygonDeliveryRangeApp = createApp({});
    const polygonDeliveryRangeElement = document.getElementById("polygon-delivery-range");

    notifications.component("notification-wrapper", NotificationWrapper);
    notifications.use(PerfectScrollbar);
    notifications.use(i18nVue, {
        resolve: async (lang) => {
            const langs = import.meta.glob("../../lang/*.json");
            return await langs[`../../lang/${lang}.json`]();
        }
    });

    dashboardApp.component("hero-widget", HeroWidget);
    dashboardApp.use(PerfectScrollbar);
    dashboardApp.use(i18nVue, {
        resolve: async (lang) => {
            const langs = import.meta.glob("../../lang/*.json");
            return await langs[`../../lang/${lang}.json`]();
        }
    });

    polygonDeliveryRangeApp.component("polygon-delivery-range-configuration", PolygonDeliveryRangeConfiguration);
    polygonDeliveryRangeApp.use(PerfectScrollbar);
    polygonDeliveryRangeApp.use(i18nVue, {
        resolve: async (lang) => {
            const langs = import.meta.glob("../../lang/*.json");
            return await langs[`../../lang/${lang}.json`]();
        }
    });

    if (dashboardAppElement) {
        dashboardApp.mount(dashboardAppElement);
    }
    if (notificationsElement) {
        notifications.mount(notificationsElement);
    }
    if (polygonDeliveryRangeElement) {
        polygonDeliveryRangeApp.mount(polygonDeliveryRangeElement);
    }
});
