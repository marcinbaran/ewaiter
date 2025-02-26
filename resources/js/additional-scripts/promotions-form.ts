import { getHostname, getPath } from "../helpers";

type InputObject = {
    container: HTMLDivElement | null;
    input: newInput | undefined;
};

enum InputType {
    money = "0",
    percent = "1",
}

document.addEventListener("DOMContentLoaded", () => {
    const percentInput: InputObject = {
        container: document.querySelector(
            "div.flex-1:has(#promotion_percent_value)",
        ),
        input: newInputInstances.find(
            (instance) => instance.input?.id === "promotion_percent_value",
        ),
    };
    const moneyInput: InputObject = {
        container: document.querySelector(
            "div.flex-1:has(#promotion_money_value)",
        ),
        input: newInputInstances.find(
            (instance) => instance.input?.id === "promotion_money_value",
        ),
    };
    const typeValueSelect: HTMLSelectElement | null = document.querySelector(
        "select[name='typeValue']",
    );

    const checkInputsVisibility = () => {
        if (typeValueSelect?.value === InputType.percent) {
            percentInput.container?.classList.add("hidden");
            if (
                percentInput.input?.input !== undefined &&
                percentInput.input?.input !== null
            ) {
                percentInput.input.input.required = false;
                percentInput.input.input.disabled = true;
            }
            moneyInput.container?.classList.remove("hidden");
            if (
                moneyInput.input?.input !== undefined &&
                moneyInput.input?.input !== null
            ) {
                moneyInput.input.input.required = true;
                moneyInput.input.input.disabled = false;
            }
        } else {
            percentInput.container?.classList.remove("hidden");
            if (
                percentInput.input?.input !== undefined &&
                percentInput.input?.input !== null
            ) {
                percentInput.input.input.required = true;
                percentInput.input.input.disabled = false;
            }
            moneyInput.container?.classList.add("hidden");
            if (
                moneyInput.input?.input !== undefined &&
                moneyInput.input?.input !== null
            ) {
                moneyInput.input.input.required = false;
                moneyInput.input.input.disabled = true;
            }
        }
    };

    const addClassesToInputs = () => {
        percentInput.input?.container?.classList.add(
            "rounded-r-none",
            "border-r-0",
        );
        moneyInput.input?.container?.classList.add(
            "rounded-r-none",
            "border-r-0",
        );
        typeValueSelect?.classList.add("border-r-0");
    };

    typeValueSelect?.addEventListener("change", () => checkInputsVisibility());

    checkInputsVisibility();
    addClassesToInputs();
});
