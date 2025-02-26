class newInput {
    parentContainer = null;
    container = null;
    input = null;
    eraseButton = null;
    inputTypeButton = null;
    type = null;

    constructor(element) {
        this.assignContainer(element);
        this.assignParentContainer();
        this.assignInput();
        if (this.type === "password") {
            this.assignInputTypeButton();
            this.addInputTypeButtonListener("password", "text");
        } else {
            this.assignEraseButton();
            this.addEraseButtonListener();
        }
        this.changeInputDisabilityState(this.input.disabled);
        this.addInputFocusListener();
    }

    assignContainer(element) {
        if (element) {
            this.container = element;
            return true;
        }
        return false;
    }

    assignParentContainer() {
        if (this.container) {
            this.parentContainer = this.container.parentNode.classList.contains(
                "new-input-parent",
            )
                ? this.container.parentNode
                : null;
            return true;
        }
        return false;
    }

    assignInput() {
        if (this.container) {
            const textarea = this.container.querySelector("textarea");
            const select = this.container.querySelector("select");
            const input = this.container.querySelector("input");
            if (textarea) {
                this.input = textarea;
                this.inputType = "textarea";
                return true;
            } else if (select) {
                this.input = select;
                this.inputType = "select";
                return true;
            } else if (input) {
                this.input = input;
                this.assignInputType();
                return true;
            }
        }
        return false;
    }

    assignInputType() {
        if (this.input) {
            this.type = this.input.type;
            return true;
        }
        return false;
    }

    assignEraseButton() {
        if (this.container) {
            this.eraseButton = this.container.querySelector(
                "button.new-input--erase-button",
            );
            return true;
        }
        return false;
    }

    assignInputTypeButton() {
        if (this.container) {
            this.inputTypeButton = this.container.querySelector(
                "button.new-input--input-type-button",
            );
            return true;
        }
        return false;
    }

    eraseInputValue() {
        if (this.input) {
            this.input.value = "";
            return true;
        }
        return false;
    }

    changeInputType(type1, type2) {
        if (this.input) {
            this.input.type = this.input.type === type1 ? type2 : type1;
            return true;
        }
        return false;
    }

    changeInputDisabilityState(state) {
        if (this.input) {
            this.input.disabled = state;
            this.type === "password"
                ? this.changeInputTypeButtonVisibility(!state)
                : this.changeEraseButtonVisibility(!state);
        }
    }

    changeEraseButtonVisibility(isVisible) {
        if (this.eraseButton) {
            if (isVisible) {
                this.eraseButton.classList.remove("hidden");
                this.eraseButton.classList.add("block");
            } else {
                this.eraseButton.classList.remove("block");
                this.eraseButton.classList.add("hidden");
            }
            return true;
        }
        return false;
    }

    changeInputTypeButtonVisibility(isVisible) {
        if (this.inputTypeButton) {
            if (isVisible) {
                this.inputTypeButton.classList.remove("hidden");
                this.inputTypeButton.classList.add("block");
            } else {
                this.inputTypeButton.classList.remove("block");
                this.inputTypeButton.classList.add("hidden");
            }
            return true;
        }
        return false;
    }

    addEraseButtonListener() {
        if (this.eraseButton) {
            this.eraseButton.addEventListener("click", () => {
                this.eraseInputValue();
            });
            return true;
        }
        return false;
    }

    addInputTypeButtonListener(type1, type2) {
        if (this.inputTypeButton) {
            this.inputTypeButton.addEventListener("click", () => {
                this.changeInputType(type1, type2);
            });
            return true;
        }
        return false;
    }

    addInputFocusListener() {
        const container = this.parentContainer || this.container;
        if (this.input) {
            this.input.addEventListener("focusin", () => {
                container.classList.add(
                    "ring-2",
                    "ring-primary-900",
                    "dark:ring-primary-700",
                );
            });
            this.input.addEventListener("focusout", () => {
                container.classList.remove(
                    "ring-2",
                    "ring-primary-900",
                    "dark:ring-primary-700",
                );
            });
            return true;
        }
        return false;
    }

    hideNewInput() {
        if (this.container) {
            this.container.classList.add("hidden");
            return true;
        }
        return false;
    }

    showNewInput() {
        if (this.container) {
            this.container.classList.remove("hidden");
            return true;
        }
        return false;
    }
}

const main = () => {
    let newInputsElements = [];
    let newInputsInstances = [];

    if (newInputsElements.length > 0) {
        newInputsElements = [];
    }
    newInputsElements = [...document.getElementsByClassName("new-input")];

    newInputsInstances = newInputsElements.map(
        (element) => new newInput(element),
    );

    return newInputsInstances;
};

document.addEventListener("rerenderNewInputs", () => {
    globalThis.newInputInstances = main();
});

globalThis.newInputInstances = main();
