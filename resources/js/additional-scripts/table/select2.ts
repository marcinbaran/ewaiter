import { getPath } from "../../helpers.js";
import { changeCreateFormType } from "../room/select2";

const isTableCreateForm = (getPath() as string).includes("tables/add");

if (isTableCreateForm) {
    document.addEventListener("select2-init", () => {
        const selectedFormType = document.getElementById(
            "adding_type",
        ) as HTMLSelectElement;
        changeCreateFormType(parseInt(selectedFormType.value), "table");
        $(selectedFormType).on("select2:select", function (e: any) {
            const selectedFormType = e.params.data.id;
            changeCreateFormType(parseInt(selectedFormType), "table");
        });
    });
}
