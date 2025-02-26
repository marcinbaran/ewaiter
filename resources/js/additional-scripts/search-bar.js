const checkIfTargetIsSearch = (target) => {
    if (target && target.classList.contains("showSearch")) {
        return true;
    } else if (target.parentElement) {
        return checkIfTargetIsSearch(target.parentElement);
    }
    return false;
};

const modal = document.getElementById("searchModal");

if (modal) {
    const form = modal.querySelector("form");
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const search = form.querySelector("input").value;
        console.log(search);
    });
}

//CLICK OUTSIDE MODAL TO CLOSE
window.addEventListener("click", (e) => {
    if (checkIfTargetIsSearch(e.target)) {
        modal.showModal();
    } else if (e.target === modal) {
        modal.close();
    }
});
