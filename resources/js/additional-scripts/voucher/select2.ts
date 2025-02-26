import { getPath } from "../../helpers.js";
import { VoucherCreateFormType } from "../../enums/VoucherCreateFormType";

const isVoucherCreateForm = (getPath() as string).includes("vouchers/add");

const changeFormType = (formType: VoucherCreateFormType) => {
    const formMultipleSection = document.getElementById(
        "voucher_quantity_container",
    ) as HTMLElement;
    const quantityNumberInput = document.getElementById(
        "voucher_quantity",
    ) as HTMLInputElement;

    if (formType === VoucherCreateFormType.SINGLE) {
        formMultipleSection.classList.add("hidden");
        quantityNumberInput.required = false;
    } else if (formType === VoucherCreateFormType.MULTIPLE) {
        formMultipleSection.classList.remove("hidden");
        quantityNumberInput.required = true;
    }
};

if (isVoucherCreateForm) {
    document.addEventListener("select2-init", () => {
        const selectedFormType = document.getElementById(
            "voucher_adding_type",
        ) as HTMLSelectElement;
        changeFormType(parseInt(selectedFormType.value));
        $(selectedFormType).on("select2:select", function (e: any) {
            const selectedFormType = e.params.data.id;
            changeFormType(parseInt(selectedFormType));
        });
    });
}
