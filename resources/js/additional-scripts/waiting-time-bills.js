import Toast from "./toast";

const billWaitTimeContainer = document.querySelector(
    ".bill-time-wait-container "
);
const billTimePickerButton = document.querySelector("#change_time_btn");
const billTimePickerInput = document.querySelector("#datetime");
const spinner = document.createElement("span");
spinner.classList.add("spinner-form");
let hasBeenSent = false;
let timeWait;

if (billWaitTimeContainer) {
    const inputTimeContainer = document.querySelector(".input-time-container");
    const displayTimeContainer = document.querySelector(
        ".display-time-container"
    );
    const loadPlaceholder = document.querySelector(".placeholder");
    const billTimeDisplay =
        displayTimeContainer.querySelector("p:nth-child(2)");
    livewire.emit("getCurrentBillStatus");

    window.addEventListener("billStatus", (e) => {
        loadPlaceholder.classList.add("hidden");
        updateDisplayForBillTime(e.detail.status);
    });

    const updateDisplayForBillTime = (billStatus) => {
        if (billStatus < 2) {
            inputTimeContainer.classList.remove("hidden");
            inputTimeContainer.classList.add("flex");
            displayTimeContainer.classList.add("hidden");
        } else {
            inputTimeContainer.classList.add("hidden");
            inputTimeContainer.classList.remove("flex");
            displayTimeContainer.classList.remove("hidden");
            const canceledDisplay = displayTimeContainer.querySelector("#canceled");
            const notCanceledDisplay = displayTimeContainer.querySelector("#not-canceled");

            if (billStatus === 4) {
                canceledDisplay.style.display = "block";
                notCanceledDisplay.style.display = "none";
            } else {
                canceledDisplay.style.display = "none";
                notCanceledDisplay.style.display = "block";
            }
        }
    };

    if (billTimePickerInput.value == "") {
        billTimePickerButton.disabled = true;
    }

    const csrfToken = billWaitTimeContainer.dataset.token;
    const url = billWaitTimeContainer.dataset.url;
    const billId = billWaitTimeContainer.dataset.billid;
    const toastSuccessMessage = billWaitTimeContainer.dataset.toastSuccess;
    const toastDangerMessage = billWaitTimeContainer.dataset.toastDanger;
    const buttonUpdateText = billWaitTimeContainer.dataset.buttonUpdate;
    const buttonSentText = billWaitTimeContainer.dataset.buttonSent;

    if (billTimePickerInput.value == "") {
        billTimePickerButton.disabled = true;
    }
    billTimePickerInput.addEventListener("change", () => {
        if (billTimePickerInput.value != "") {
            billTimePickerButton.disabled = false;
        } else {
            billTimePickerButton.disabled = true;
        }
        if (hasBeenSent) {
            billTimePickerButton.innerText = buttonUpdateText;
        }
    });

    billTimePickerButton.addEventListener("click", () => {
        let billTimeValue = billTimePickerInput.value;
        let data = {
            pk: billId,
            time_wait: billTimeValue,
            _token: csrfToken
        };

        billTimePickerButton.append(spinner);
        if (billTimeValue != "") {
            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(data)
            })
                .then((response) => response.json())
                .then((data) => {
                    billTimePickerButton.removeChild(spinner);
                    billTimeDisplay.innerText = billTimeValue;
                    if (data.status == "ok") {
                        billTimePickerButton.disabled = true;
                        hasBeenSent = true;
                        billTimePickerButton.innerText = buttonSentText;
                        Toast.success(toastSuccessMessage);
                    } else {
                        Toast.danger(toastDangerMessage);
                    }
                })
                .catch((error) => {
                    billTimePickerButton.removeChild(spinner);
                    Toast.danger(toastDangerMessage);
                    console.error("Error:", error);
                });
        } else {
            Toast.danger(toastDangerMessage);
        }
    });
}

window.addEventListener("toastActualOrders", (e) => {
    console.log(e);
    if (e.detail.hasOwnProperty("message")) {
        Toast.success(e.detail.message);
    }
});

window.addEventListener("toastActualOrdersTimeWaitError", (e) => {
    if (e.detail.hasOwnProperty("message")) {
        Toast.danger(e.detail.message);
    }
});
