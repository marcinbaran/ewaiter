import $ from "jquery";
import "select2";

import { select2Init } from "./libraries";

const initializeExternalLibraries = () => {
    window.$ = window.jQuery = $;
};

const initializeInternalLibraries = () => {
    document.addEventListener("initSelect2", () => {
        select2Init();
    });
    select2Init();
};

const initializeLibraries = () => {
    initializeExternalLibraries();
    initializeInternalLibraries();
};

export { initializeLibraries };
