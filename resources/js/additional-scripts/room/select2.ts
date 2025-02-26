import { getPath } from "../../helpers.js";
import { TableRoomCreateFormType } from "../../enums/TableRoomCreateFormType";

const isRoomCreateForm = (getPath() as string).includes("rooms/add");

export const changeCreateFormType = (
    formType: TableRoomCreateFormType,
    objectType: "room" | "table",
) => {
    const formSingleSection = document.getElementById(
        "adding_single",
    ) as HTMLElement;
    const nameInput = document.getElementById(
        `${objectType}_name`,
    ) as HTMLInputElement;
    const numberInput = document.getElementById(
        `${objectType}_number`,
    ) as HTMLInputElement;

    const formRangeSection = document.getElementById(
        "adding_range",
    ) as HTMLElement;
    const fromNumberInput = document.getElementById(
        `${objectType}_from_number`,
    ) as HTMLInputElement;
    const toNumberInput = document.getElementById(
        `${objectType}_to_number`,
    ) as HTMLInputElement;

    if (formType === TableRoomCreateFormType.SINGLE) {
        formRangeSection.style.display = "none";
        fromNumberInput.required = false;
        toNumberInput.required = false;

        formSingleSection.style.display = "flex";
        nameInput.required = true;
        numberInput.required = true;
    } else if (formType === TableRoomCreateFormType.RANGE) {
        formRangeSection.style.display = "flex";
        fromNumberInput.required = true;
        toNumberInput.required = true;

        formSingleSection.style.display = "none";
        nameInput.required = false;
        numberInput.required = false;
    }
};

if (isRoomCreateForm) {
    document.addEventListener("select2-init", () => {
        const selectedFormType = document.getElementById(
            "adding_type",
        ) as HTMLSelectElement;
        changeCreateFormType(parseInt(selectedFormType.value), "room");
        $(selectedFormType).on("select2:select", function (e: any) {
            const selectedFormType = e.params.data.id;
            changeCreateFormType(parseInt(selectedFormType), "room");
        });
    });
}
