function scroll() {
    const miniCart = document.querySelector("#miniCart");

    if (miniCart) {
        const nav = document.querySelector("nav").getBoundingClientRect();
        const child = miniCart.children[0];
        const cart = miniCart.getBoundingClientRect();
        const footer = document.querySelector("footer");

        let inView = false;
        let options = {
            root: document.querySelector("#main-content"),
            rootMargin: "0px",
            threshold: 0.25
        };

        let observer = new IntersectionObserver((entries) => {
            inView = entries[0].isIntersecting;
            if (inView) {
                child.classList.add("h-[80vh]");
                child.classList.remove("h-[90vh]");
            }
        }, options);
        observer.observe(footer);

        if (cart.y === nav.height) {
            child.classList.add("h-[90vh]");
            child.classList.remove("h-[80vh]");
        } else {
            child.classList.add("h-[80vh]");
            child.classList.remove("h-[90vh]");
        }
    }
}

document.querySelector("#main-content").addEventListener("scroll", scroll);

