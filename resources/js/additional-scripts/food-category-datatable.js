import { getHostname, getPath } from "../helpers";
import { initPS } from "./perfect-scrollbar";

const tableBody = document.querySelector(".food-category-body");

let data = [];
let headerRows = [];
let depth = 0;

let tableObject = [];

const constructTable = (data, object) => {
    data.forEach((element, index) => {
        object[index] = {
            id: constructColumn(element.id),
            name: constructColumn(element.name),
            photo: constructPhotoColumn(element.photo),
            description: constructColumn(element.description),
            position: constructColumn(element.position),
            visibility: constructColumn(element.visibility),
            actions: constructActionsColumn(element.actions),
            children: constructTable(element.children, []),
        };
    });
    return object;
};

const renderTable = (data, body, isChildren) => {
    const table = document.createElement("table");
    table.appendChild(constructHeaderRow(headerRows));
    table.classList.add("min-w-full");
    const column = constructColumn("");
    const placeholderRow = document.createElement("tr");
    column.colSpan = 8;
    column.classList.remove("px-6");
    column.classList.add("pl-6");

    data.forEach((element, index) => {
        const deleteButton = element.actions.querySelector(".delete");
        if (deleteButton !== null) {
            deleteButton?.addEventListener("click", (e) => {
                const deleteId = deleteButton.dataset.deleteid;
                window.livewire.emit("deleteEvent", deleteId);
            });
        }
        let row = constructRow();
        if (index % 2 != 0) {
            row.classList.remove("dark:bg-gray-700", "dark:text-white");
            row.classList.add(
                "bg-gray-50",
                "dark:bg-gray-800",
                "dark:text-gray-400",
            );
        }
        if (element.children.length > 0) {
            row.appendChild(constructExpandButton(row));
        } else {
            row.appendChild(constructColumn(""));
        }
        row.appendChild(element.id);
        row.appendChild(element.name);
        row.appendChild(element.photo);
        row.appendChild(element.description);
        row.appendChild(element.position);
        row.appendChild(element.visibility);
        row.appendChild(element.actions);

        if (isChildren) {
            depth++;
            placeholderRow.classList.add("hidden", "relative");
            table.appendChild(row);
            column.appendChild(table);
            placeholderRow.appendChild(column);
            body.appendChild(placeholderRow);
        } else {
            depth = 0;
            body.appendChild(row);
        }

        if (element.children.length > 0) {
            if (depth >= 1) {
                renderTable(element.children, table, true);
            } else {
                renderTable(element.children, body, true);
            }
        }
    });
};

const constructColumn = (data) => {
    const column = document.createElement("td");
    column.classList.add(
        "px-6",
        "py-4",
        "whitespace-nowrap",
        "text-sm",
        "font-medium",
        "dark:text-white",
    );
    column.innerHTML = data;
    return column;
};

const constructTreeLine = () => {
    const div = document.createElement("div");
    div.classList.add(
        "absolute",
        "border-l-2",
        "border-b-2",
        "radius-bl-lg",
        "border-gray-500",
        "w-4",
        "h-12",
        "bottom-12",
        "left-10",
        "hidden",
    );
    return div;
};
const constructActionsColumn = (data) => {
    const column = document.createElement("td");
    column.classList.add(
        "px-6",
        "py-4",
        "whitespace-nowrap",
        "text-sm",
        "font-medium",
        "dark:text-white",
    );
    column.innerHTML = data;
    return column;
};

const constructPhotoColumn = (data) => {
    const column = document.createElement("td");
    column.classList.add(
        "px-6",
        "py-4",
        "whitespace-nowrap",
        "text-sm",
        "font-medium",
        "dark:text-white",
    );
    const img = document.createElement("img");
    img.src = data;
    column.appendChild(img);
    return column;
};

const constructRow = () => {
    const row = document.createElement("tr");
    row.classList.add(
        "bg-white",
        "dark:bg-gray-700",
        "dark:text-white",
        "relative",
    );
    return row;
};

const constructHeaderRow = (data) => {
    const row = document.createElement("tr");
    row.classList.add(
        "bg-gray-100",
        "dark:bg-gray-800",
        "dark:text-gray-400",
        "[&>*:last-child]:rounded-tr-lg",
        "[&>*:first-child]:rounded-tl-lg",
    );
    row.appendChild(constructColumn(""));
    data.forEach((element) => {
        const column = document.createElement("th");
        column.classList.add(
            "px-6",
            "py-3",
            "text-left",
            "text-xs",
            "font-medium",
            "uppercase",
            "tracking-wider",
            "dark:text-white",
        );
        column.textContent = element;
        row.appendChild(column);
    });
    return row;
};

const constructExpandButton = (row) => {
    const button = document.createElement("button");
    button.classList.add(
        "px-2",
        "py-1",
        "rounded-md",
        "dark:text-white",
        "bg-none",
        "relative",
    );
    button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
    <path d="M6 9l6 6l6 -6"></path>
    </svg>`;
    const column = constructColumn("");

    const treeLine = constructTreeLine();
    treeLine.classList.add("rotate-180");
    button.appendChild(treeLine);
    button.addEventListener("click", () => {
        button.classList.toggle("rotate-180");
        row.nextElementSibling.classList.toggle("hidden");
        treeLine.classList.toggle("hidden");
    });
    column.appendChild(button);
    return column;
};

if (tableBody) {
    window.livewire.emit("sendData");
    window.addEventListener("foodCategoryDatatableData", (e) => {
        tableObject = [];
        data = e.detail.data;
        headerRows = e.detail.headerRows;
        constructTable(data, tableObject);
        renderTable(tableObject, tableBody, false);
    });
}
