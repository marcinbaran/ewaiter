import Alpine from "alpinejs";
import jQuery from "jquery";
import Datepicker from "flowbite-datepicker/Datepicker";
import { TimepickerUI } from "timepicker-ui";
import Toast from "./additional-scripts/toast";
import flatpickr from "./additional-scripts/flatpickr";
import iconPicker from "./additional-scripts/icon-picker";

// initialize alpinejs
window.Alpine = Alpine;
Alpine.start();

// initialize jquery
window.$ = window.jQuery = jQuery;

// initialize flowbite's datepicker
const datepickerElIndicator = document.querySelectorAll(
    "input.datepicker-ui-input",
);
datepickerElIndicator.forEach((indicator) => {
    new Datepicker(indicator, {
        format: "yyyy-mm-dd",
        language: "pl",
        autoHide: true,
        weekStart: 1,
        minDate: new Date(),
    });
});

// initialize timepicker-ui
const timePickerElements = document.querySelectorAll(".timepicker");
timePickerElements.forEach((indicator) => {
    const myTimePicker = new TimepickerUI(indicator, {
        clockType: "24h",
    });
    myTimePicker.create();
});

// initialize toast
Toast.space();

// initialize flatpickr
flatpickr.init();

// initialize icon picker
document.addEventListener("DOMContentLoaded", iconPicker.init);
