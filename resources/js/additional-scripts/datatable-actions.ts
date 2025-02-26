import Swal from "sweetalert2";

class DeleteHandler {
    private deleteElements: NodeListOf<Element>;
    private swalBootstrap: any;

    constructor() {
        // this.deleteElements = document.querySelectorAll('[data-type="delete"]');
        // this.swalBootstrap = Swal.mixin({
        //     buttonsStyling: false,
        // });
        // this.attachClickListeners();
    }

    private attachClickListeners(): void {
        this.deleteElements.forEach((element) => {
            element.addEventListener(
                "click",
                this.handleDeleteClick.bind(this),
            );
        });
    }

    private handleDeleteClick(event: MouseEvent): void {
        event.preventDefault();

        const csrf = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        const deleteUrl = (
            event.target as HTMLElement
        ).parentElement.getAttribute("data-url");

        this.swalBootstrap
            .fire({
                title: "Czy na pewno chcesz usunąć ten element?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Usuń",
                cancelButtonText: "Anuluj",
                dangerMode: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    const xhr = new XMLHttpRequest();
                    xhr.open("DELETE", deleteUrl);
                    xhr.setRequestHeader("X-CSRF-Token", csrf);

                    xhr.onload = () => {
                        if (xhr.status === 200) {
                            this.swalBootstrap.fire(
                                "Sukces",
                                "Element został usunięty",
                                "success",
                            );
                        } else {
                            console.log(xhr);
                            this.swalBootstrap.fire(
                                "Błąd",
                                "Wystąpił błąd podczas usuwania elementu",
                                "error",
                            );
                        }
                    };
                    xhr.send();
                }
            });
    }
}

const deleteHandler = new DeleteHandler();
