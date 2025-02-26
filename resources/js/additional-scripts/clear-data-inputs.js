const timepickerContainer = document.querySelectorAll(".timepicker");

const disableButton = (element, isDisabled, classToAdd, classRemove) => {
    element.disabled = isDisabled;
    element.classList.add(classToAdd);
    element.classList.remove(classRemove);
};

if (timepickerContainer) {
    timepickerContainer.forEach((container) => {
        const timepickerInput = container.querySelector(".timepicker-ui-input");
        const timepickerClearBtn = container.querySelector(
            ".timepicker-clear-btn",
        );
        if (timepickerInput.value === "") {
            disableButton(
                timepickerClearBtn,
                true,
                "cursor-not-allowed",
                "cursor-pointer",
            );
        }

        container.addEventListener("accept", () => {
            if (timepickerInput.value) {
                disableButton(
                    timepickerClearBtn,
                    false,
                    "cursor-pointer",
                    "cursor-not-allowed",
                );
            } else {
                disableButton(
                    timepickerClearBtn,
                    true,
                    "cursor-not-allowed",
                    "cursor-pointer",
                );
            }
        });

        timepickerClearBtn.addEventListener("click", () => {
            timepickerInput.value = "";
            disableButton(
                timepickerClearBtn,
                true,
                "cursor-not-allowed",
                "cursor-pointer",
            );
        });
    });
}
