const daypickers = document.querySelectorAll(".weekdayspicker .daypicker");
if (daypickers) {
    daypickers.forEach((daypicker) => {
        const inputs = daypicker.querySelectorAll("input");
        const input = [...inputs].filter((input) => input.id)[0];
        const label = daypicker.querySelector("label");

        input.checked
            ? label.classList.add("active")
            : label.classList.remove("active");

        input.addEventListener("change", () => {
            if (input.checked) {
                label.classList.add("active");
                input.checked = true;
            } else {
                label.classList.remove("active");
                input.checked = false;
            }
        });
    });
}
