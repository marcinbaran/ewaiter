const btn = document.querySelector("#collapsible");
const categoriesContainer = document.querySelector("#categories");
const dropDown = document.querySelector("#dropdown");

// console.log(btn)

if (btn !== null) {
    btn.addEventListener("click", () => {
        dropDown.classList.toggle("hidden");
    });

    categoriesContainer.addEventListener("mouseleave", () => {
        dropDown.classList.add("hidden");
    });
}
