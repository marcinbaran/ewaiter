import Toast from "./toast";

const formStyles = "flex flex-row gap-2 items-center justify-center";
const inputStyles =
    "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500";
const confirmButtonStyles =
    "bg-green-100 text-green-800 text-sm font-semibold inline-flex items-center rounded-full p-1.5 aspect-square hover:bg-green-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-green-400";
const declineButtonStyles =
    "bg-red-100 text-red-800 text-sm font-semibold inline-flex items-center rounded-full p-1.5 aspect-square hover:bg-green-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-red-400";
const toastSpaceStyles = "fixed top-0 right-0 z-50 flex flex-col gap-2 p-4";

const checkIcon =
    '<svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/></svg>';
const closeIcon =
    '<svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="m9.414 8 5.293-5.293a1 1 0 1 0-1.414-1.414L8 6.586 2.707 1.293a1 1 0 0 0-1.414 1.414L6.586 8l-5.293 5.293a1 1 0 1 0 1.414 1.414L8 9.414l5.293 5.293a1 1 0 0 0 1.414-1.414L9.414 8Z"/></svg>';

const createInput = ({ type, validation, options, value }) => {
    let input = null;
    if (type === "select") {
        input = document.createElement("select");
        input.classList.add("editable-select");
        inputStyles.split(" ").forEach((style) => input.classList.add(style));
        let isElementSelected = false;
        for (const option of options) {
            const optionElement = document.createElement("option");
            optionElement.value = option.value;
            optionElement.text = option.label;
            if (!isElementSelected && option.selected) {
                optionElement.selected = true;
                isElementSelected = true;
            }
            input.add(optionElement);
        }
    } else {
        input = document.createElement("input");
        input.classList.add("editable-input");
        inputStyles.split(" ").forEach((style) => input.classList.add(style));
        input.type = type;
        input.value = value;
        type !== "password" && (input.placeholder = value);
        if (validation) {
            validation.required && (input.required = true);
            validation.min && (input.min = validation.min);
            validation.max && (input.max = validation.max);
            validation.minLength && (input.minLength = validation.minLength);
            validation.maxLength && (input.maxLength = validation.maxLength);
            validation.pattern && (input.pattern = validation.pattern);
        }
    }
    return input;
};

const createConfirmButton = () => {
    const confirmBtn = document.createElement("button");
    confirmBtn.classList.add("editable-confirm");
    confirmButtonStyles.split(" ").forEach((style) => {
        confirmBtn.classList.add(style);
    });
    confirmBtn.type = "submit";
    confirmBtn.innerHTML = checkIcon;
    return confirmBtn;
};

const createDeclineButton = (element, attributes) => {
    const declineBtn = document.createElement("button");
    declineBtn.classList.add("editable-decline");
    declineButtonStyles.split(" ").forEach((style) => {
        declineBtn.classList.add(style);
    });
    declineBtn.addEventListener("click", (e) => {
        element.innerHTML = attributes.value;
        window.livewire.emit("refreshDatatable");
    });
    declineBtn.innerHTML = closeIcon;
    return declineBtn;
};

const confirmFetch = async (params) => {
    const { url, id, model, column, value, token } = params;
    const response = await fetch(url, {
        method: "PUT",
        body: JSON.stringify({ id, model, column, value }),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
        },
    });
    if (!response.ok) {
        throw new Error("Network response was not ok");
    }
    return response;
};

const getAttributes = (element) => {
    const { type, options, validation, url, id, model, column } =
        element.dataset;
    const attributes = {};

    if (
        type === undefined ||
        url === undefined ||
        id === undefined ||
        model === undefined ||
        column === undefined
    ) {
        throw new Error(
            "Editable element must have a data-type, data-url, data-id, data-model and data-column attributes!",
        );
    }

    if (type === "select" && options === undefined) {
        throw new Error(
            "Editable element with select type must have a data-options attribute!",
        );
    }

    if (
        !(
            type === "select" ||
            type === "text" ||
            type === "number" ||
            type === "email" ||
            type === "password" ||
            type === "time" ||
            type === "date"
        )
    ) {
        throw new Error(
            "Editable element must have a valid data-type attribute! Eligible types: text, number, email, password, select, time, date",
        );
    }

    attributes.value = element.innerText;
    attributes.type = element.dataset.type;
    attributes.options =
        attributes.type === "select"
            ? JSON.parse(element.dataset.options)
            : null;
    attributes.validation = element.dataset.validation
        ? JSON.parse(element.dataset.validation)
        : null;
    attributes.fetch = {};
    attributes.fetch.url = element.dataset.url;
    attributes.fetch.id = element.dataset.id;
    attributes.fetch.model = element.dataset.model;
    attributes.fetch.column = element.dataset.column;
    attributes.fetch.token = document.querySelector(
        'meta[name="csrf-token"]',
    ).content;
    attributes.toast = {};
    attributes.toast.success = element.dataset.toastSuccess;
    attributes.toast.danger = element.dataset.toastDanger;
    return attributes;
};

const editElement = (element) => {
    const attributes = getAttributes(element);

    const form = document.createElement("form");
    form.classList.add("editable-form");
    formStyles.split(" ").forEach((style) => form.classList.add(style));

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        let value = input.value;
        if (attributes.type === "select") {
            attributes.options.find((option) => option.value === value);
        } else if (attributes.type === "number") value = Number(value);
        else if (attributes.type === "email") value = value.toLowerCase();
        element.innerHTML =
            attributes.type === "select"
                ? attributes.options.find((option) => option.value == value)
                      .label
                : value;
        confirmFetch({ ...attributes.fetch, value })
            .then(() => {
                Toast.success(attributes.toast.success);
                window.livewire.emit("refreshDatatable");
            })
            .catch((error) => {
                Toast.danger(attributes.toast.danger);
                console.error(error);
                window.livewire.emit("refreshDatatable");
            });
    });

    const input = createInput(attributes);
    const confirmBtn = createConfirmButton();
    const declineBtn = createDeclineButton(element, attributes);

    form.appendChild(input);
    form.appendChild(confirmBtn);
    form.appendChild(declineBtn);

    element.innerHTML = "";
    element.appendChild(form);
    form.focus();
};

const checkIfTargetIsEditable = (target) => {
    if (target && target.classList.contains("editable")) {
        return target;
    } else if (target && target.classList.contains("editable-form")) {
        return null;
    } else if (target.parentElement) {
        return checkIfTargetIsEditable(target.parentElement);
    }
    return null;
};

document.addEventListener("click", (e) => {
    const editableElement = checkIfTargetIsEditable(e.target);
    editableElement && editElement(editableElement);
});
