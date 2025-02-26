const pagination = document.querySelector(
    "nav[aria-label='Pagination Navigation']",
);
const main = document.querySelector("#main-content");
if (pagination) {
    window.addEventListener("click", (event) => {
        if (
            event.target.attributes["wire:click"] ||
            event.target.parentElement?.attributes["wire:click"]
        ) {
            main.scrollTop = 0;
        }
    });
}
