import $ from "jquery";
import "select2";
import { getPath } from "../../helpers.js";

const isReservationForm =
    (getPath() as string).includes("reservations/add") ||
    (getPath() as string).includes("reservations/edit");

let labelInnerHtml = null;

const requireTable = (isRequired: boolean) => {
    const tablesNewInput = window.newInputInstances.filter((newInput: any) => {
        return (
            newInput.inputType === "select" &&
            newInput.input.name === "table_id"
        );
    });
    const label =
        tablesNewInput[0].parentContainer.parentElement.parentElement?.querySelector(
            "label",
        );
    if (tablesNewInput.length > 0) {
        tablesNewInput[0].input.required = isRequired;
        if (labelInnerHtml === null) {
            labelInnerHtml = label.innerHTML;
        }
        label.innerHTML = isRequired
            ? labelInnerHtml
            : labelInnerHtml + ' <span class="font-light">(opcjonalne)</span>';
    }
};

if (isReservationForm) {
    let select2ListenerAdded = 0;
    const statusCancelled = 2;

    document.addEventListener("select2-init", () => {
        if (select2ListenerAdded === 2) {
            const selectedStatus = $("#reservation_status").val();
            requireTable(selectedStatus != statusCancelled);
            $("#reservation_status").on("select2:select", function (e: any) {
                const selectedStatus = e.params.data.id;
                requireTable(selectedStatus != statusCancelled);
            });
        }
        select2ListenerAdded++;
    });
}
