const settingsForm = document.querySelector("form#setting");
const settingsMenuButton = document.querySelector(".settings-menu-button");
const settingsMenu = document.querySelector(".settings-menu");
const settingsContainer = document.querySelector(".settings-container");

const convertRangeToInputs = (inputHidden, inputFrom, inputTo) => {
    const combinedTimeValue = `${inputFrom.value}-${inputTo.value}`;
    inputHidden.value = combinedTimeValue;
    return combinedTimeValue;
};

const convertInputsToRange = (inputHidden, inputFrom, inputTo) => {
    const timeValues = inputHidden.value.split("-");
    inputFrom.value = timeValues[0];
    inputTo.value = timeValues[1];
    return timeValues;
};

const changeInputParameters = (isEnabled, input) => {
    if (input.type === "textarea") {
        input.disabled = !isEnabled;
    } else {
        const newInputInstance = newInputInstances.find(
            (newInput) => newInput.input === input
        );
        if (newInputInstance) {
            newInputInstance.changeInputDisabilityState(!isEnabled);
        }
    }
    input.required = isEnabled;
};

const toggleSettingInput = (settingElement) => {
    const settingAttributes = {
        type: settingElement.dataset.settingType,
        name: settingElement.dataset.setting
    };
    const inputGroup = {
        toggle: settingElement.querySelector(
            `input[type=checkbox][name="value_active[${settingAttributes.name}]"]`
        ),
        input: null
    };
    if (settingAttributes.type === "textarea") {
        inputGroup.input = settingElement.querySelector(`textarea`);
    } else if (settingAttributes.type === "time-range") {
        inputGroup.input = {
            from: settingElement.querySelector(
                "input[id^=\"time-range-\"][id$=\"-from\"]"
            ),
            to: settingElement.querySelector(
                "input[id^=\"time-range-\"][id$=\"-to\"]"
            )
        };
    } else {
        inputGroup.input = settingElement.querySelector(
            `input[name="value[${settingAttributes.name}]"]:not([type="hidden"])`
        );
    }
    if (inputGroup.input) {
        if (settingAttributes.type == "time-range") {
            changeInputParameters(
                inputGroup.toggle.checked,
                inputGroup.input.from
            );
            changeInputParameters(
                inputGroup.toggle.checked,
                inputGroup.input.to
            );
            inputGroup.toggle.addEventListener("change", () => {
                changeInputParameters(
                    inputGroup.toggle.checked,
                    inputGroup.input.from
                );
                changeInputParameters(
                    inputGroup.toggle.checked,
                    inputGroup.input.to
                );
            });
        } else {
            changeInputParameters(inputGroup.toggle.checked, inputGroup.input);
            inputGroup.toggle.addEventListener("change", () =>
                changeInputParameters(
                    inputGroup.toggle.checked,
                    inputGroup.input
                )
            );
        }
    }
};

if (settingsMenuButton && settingsMenu && settingsContainer) {
    settingsMenuButton.addEventListener("click", () => {
        if (window.innerWidth < 768) {
            settingsMenu.classList.toggle("active-menu");
        }
    });

    window.addEventListener("click", (e) => {
        if (
            !settingsMenu.contains(e.target) &&
            !settingsMenuButton.contains(e.target)
        ) {
            settingsMenu.classList.remove("active-menu");
        }
    });

    const timeRangeInputs = [...document.getElementsByClassName("time-range")];
    if (timeRangeInputs.length > 0) {
        timeRangeInputs.forEach((element) => {
            const inputs = {
                hidden: element.querySelector("input[type=hidden]"),
                from: element.querySelector("[id^='time-range'][id$='from']"),
                to: element.querySelector("[id^='time-range'][id$='to']")
            };
            if (inputs.hidden && inputs.from && inputs.to) {
                inputs.from.addEventListener("change", () => {
                    convertRangeToInputs(inputs.hidden, inputs.from, inputs.to);
                });
                inputs.to.addEventListener("change", () => {
                    convertRangeToInputs(inputs.hidden, inputs.from, inputs.to);
                });
                convertInputsToRange(inputs.hidden, inputs.from, inputs.to);
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const settings = document.querySelectorAll(".setting");
        settings.forEach((setting) => toggleSettingInput(setting));
    });
}
