const form = document.querySelector(".form");
const formButton = document.querySelector(".button-form");

if (form) {
    form.addEventListener("submit", () => {
        formButton.classList.add("spinner-form");
    });
}
