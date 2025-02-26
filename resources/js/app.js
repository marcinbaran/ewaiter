import "flowbite";

import { initializeLibraries } from "./initializers";

import "./utils";

import "./additional-scripts";

import "./additional-scripts/charts";
import "./additional-scripts/datatable-actions";
import "./additional-scripts/daypicker";
import "./additional-scripts/editable";
import "./additional-scripts/filepond";
import "./additional-scripts/flowbite";
import "./additional-scripts/imask";
import "./additional-scripts/promotions-form";
import "./additional-scripts/map";
import "./additional-scripts/notifications";
import "./additional-scripts/perfect-scrollbar";
import "./additional-scripts/search-bar";
import "./additional-scripts/theme-switcher";
import "./additional-scripts/settings-menu";
import "./additional-scripts/standard-s2";
import "./additional-scripts/hammer";
import "./additional-scripts/datatable";
import "./additional-scripts/timepicker-ui";
import "./additional-scripts/form-spinner";
import "./additional-scripts/clear-data-inputs";
import "./additional-scripts/new-input";
import "./additional-scripts/waiting-time-bills";
import "./additional-scripts/food-category-datatable";
import "./additional-scripts/vue";
import "./additional-scripts/i18n";
import "./additional-scripts/slugifyInput";
import "./additional-scripts/marketplace/dropdown"
import "./additional-scripts/form-translation"
import "./additional-scripts/marketplace/scroll-cart"

import * as Sentry from "@sentry/browser";

import "../css/app.css";

initializeLibraries();

Sentry.init({
    dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC
});
