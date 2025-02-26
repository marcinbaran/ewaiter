import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Polish } from "flatpickr/dist/l10n/pl.js";
import { German } from "flatpickr/dist/l10n/de.js";
import { Default } from "flatpickr/dist/l10n/default.js";

class FlatpickrInstance {
    static #instances = [];

    constructor(element, type, locale = "en", options = {}) {
        this.element = element;
        const attributes = this.#getElementAttributes();
        this.options = {
            ...this.#generateOptions(type, attributes.step),
            locale: this.#getLocale(locale),
            minDate: attributes.min,
            maxDate: attributes.max,
            minTime: attributes.minTime,
            ...options,
        };
    }

    static destroyAll() {
        if (this.#instances.length > 0) {
            this.#instances.forEach((instance) => {
                instance.destroy();
            });
        }
    }

    static getInstances() {
        return this.#instances;
    }

    init() {
        this.instance = flatpickr(this.element, this.options);
        FlatpickrInstance.#instances.push(this);
    }

    destroy() {
        this.instance.destroy();
    }

    #generateOptions(type, step) {
        const options = {};
        if (type === "date") {
            options.enableTime = false;
            options.dateFormat = "Y-m-d";
        } else if (type === "time") {
            options.enableTime = true;
            options.time_24hr = true;
            options.noCalendar = true;
            options.dateFormat = "H:i";
            if (step) {
                options.timeIncrement = step ?? 5;
            }
        } else {
            if (type !== "datetime") {
                console.warn(
                    "Invalid type. Available types: date, time, datetime. Setting default type: datetime",
                );
            }
            options.enableTime = true;
            options.time_24hr = true;
            options.dateFormat = "Y-m-d H:i";
            if (step) {
                options.minuteIncrement = step ?? 5;
            }
        }
        return options;
    }

    #getLocale(locale) {
        switch (locale) {
            case "en":
                return Default;
            case "pl":
                return Polish;
            case "de":
                return German;
            default:
                throw new Error(
                    "Invalid locale. Available locales: en, pl, de",
                );
        }
    }

    #getElementAttributes() {
        const attributes = {
            min: this.element.min ?? null,
            max: this.element.max ?? null,
            minTime: this.element.attributes.mintime?.value ?? null,
            step: this.element.attributes.step?.value ? parseInt(this.element.attributes.step.value, 10) : null,
        };
        return attributes;
    }
}

const flatpickrInit = () => {
    FlatpickrInstance.destroyAll();

    const options = {};

    const language = document.documentElement.lang;

    const datepickerElements = [
        ...document.getElementsByClassName("flatpickr-datepicker"),
    ];

    datepickerElements.forEach((element) => {
        const instance = new FlatpickrInstance(
            element,
            "date",
            language,
            options,
        );
        instance.init();
    });

    const timepickerElements = [
        ...document.getElementsByClassName("flatpickr-timepicker"),
    ];

    timepickerElements.forEach((element) => {
        const instance = new FlatpickrInstance(
            element,
            "time",
            language,
            options,
        );
        instance.init();
    });

    const datetimepickerElements = [
        ...document.getElementsByClassName("flatpickr-datetimepicker"),
    ];

    datetimepickerElements.forEach((element) => {
        const instance = new FlatpickrInstance(
            element,
            "datetime",
            language,
            options,
        );
        instance.init();
    });
};

const main = {
    init: flatpickrInit,
};

export default main;
