import IMask from "imask";

let iMaskIndicators = document.querySelectorAll(".input-mask");
const increaseInputValueButton = document.querySelector("#increase");
const decreaseInputValueButton = document.querySelector("#decrease");

let iMaskArray = [];

function iMaskInit() {
    iMaskIndicators.forEach((indicator) => {
        indicator.type = "text";
        const predefined = indicator.getAttribute("format");
        const min = parseFloat(indicator.getAttribute("format-min"));
        const max = parseFloat(indicator.getAttribute("format-max"));
        let options = {};
        if (!predefined) options.mask = indicator.getAttribute("mask");
        else {
            switch (predefined) {
                case "money":
                    options = {
                        mask: Number,
                        min: !isNaN(min) ? min : 0,
                        max: !isNaN(max) ? max : 9999,
                        scale: 2,
                        thousandsSeparator: "",
                        padFractionalZeros: false,
                        normalizeZeros: false,
                        radix: ".",
                        mapToRadix: [","],
                    };
                    !isNaN(max) && (options.max = max);
                    break;
                case "phone":
                    options = {
                        mask: "000 000 000",
                    };
                    break;
                case "postal-code":
                    options = {
                        mask: "00-000",
                    };
                    break;
                case "bank-account":
                    options = {
                        mask: "00 0000 0000 0000 0000 0000 0000",
                    };
                    break;
                case "integer":
                    options = {
                        mask: Number,
                        min: 0,
                        max: 1440,
                        scale: 0,
                        thousandsSeparator: "",
                        padFractionalZeros: false,
                        normalizeZeros: true,
                    };
                case "percentage":
                    options = {
                        mask: Number,
                        min: !isNaN(min) ? min : 0,
                        max: !isNaN(max) ? max : 100,
                        scale: 0,
                        thousandsSeparator: "",
                        padFractionalZeros: false,
                        normalizeZeros: true,
                    };
            }
        }
        const mask = IMask(indicator, options);
        iMaskArray.push(mask);
    });
}

if (iMaskIndicators) {
    iMaskInit();
}

window.addEventListener("initIMask", () => {
    iMaskIndicators = document.querySelectorAll(".input-mask");
    iMaskInit();
});

window.addEventListener("resetInputValue", (e) => {
    iMaskArray.forEach((mask) => {
        mask.value = "";
        mask.updateValue();
    });
});

const debounce = (func, wait) => {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
};
const emitUpdateWaitTimeValue = debounce((value) => {
    window.livewire.emit("updateWaitTimeValue", value);
}, 250);

if (increaseInputValueButton && decreaseInputValueButton) {
    increaseInputValueButton.addEventListener("click", (e) => {
        iMaskArray.forEach((mask) => {
            if (mask.value === "") mask.value = "0";
            const newValue = parseInt(mask.value) + 5;
            mask.value = String(newValue);
            mask.updateValue();
            emitUpdateWaitTimeValue(mask.value);
        });
    });

    decreaseInputValueButton.addEventListener("click", (e) => {
        iMaskArray.forEach((mask) => {
            const newValue =
                parseInt(mask.value) - 5 > 0 ? parseInt(mask.value) - 5 : 0;
            mask.value = String(newValue);
            mask.updateValue();
            emitUpdateWaitTimeValue(mask.value);
        });
    });
}
