import { compareArrays, getHostname, getTokenCSRF } from "../helpers";
import createTranslationButton from "./translation-button";

const tabContent: HTMLDivElement | null = document.querySelector(".tab-content");

if (tabContent) {
    const individualTabs: HTMLDivElement[] = Array.from(tabContent.querySelectorAll("div[role='tabpanel']"));
    const buttonText = tabContent.dataset.buttonTranslation!;
    const toastDangerMessage = tabContent.dataset.toastDanger!;

    const elements: { [key: string]: { [locale: string]: HTMLInputElement | HTMLTextAreaElement | null } } = {};

    individualTabs.forEach((tab: HTMLDivElement) => {
        const inputs = tab.querySelectorAll("input[id]");
        const textareas = tab.querySelectorAll("textarea[id]");

        inputs.forEach((input: HTMLInputElement) => {
            const id = input.id;
            const locale = id.split('_').pop()!;
            const baseId = id.slice(0, -(locale.length + 1));

            if (!elements[baseId]) {
                elements[baseId] = {};
            }
            elements[baseId][locale] = input;
        });

        textareas.forEach((textarea: HTMLTextAreaElement) => {
            const id = textarea.id;
            const locale = id.split('_').pop()!;
            const baseId = id.slice(0, -(locale.length + 1));

            if (!elements[baseId]) {
                elements[baseId] = {};
            }
            elements[baseId][locale] = textarea;
        });
    });

    individualTabs.forEach((tab: HTMLDivElement) => {
        const labels = tab.querySelectorAll("label");

        labels.forEach((label: HTMLLabelElement) => {
            const inputId = label.getAttribute("for");
            if (!inputId) {
                return;
            }

            const baseId = inputId.slice(0, -3);
            const targetLocale = inputId.split('_').pop();

            if (targetLocale !== "pl" && elements[baseId] && elements[baseId]["en"] && elements[baseId]["pl"]) {
                if (!label.querySelector('.translation-button')) {
                    const otherLocale = "pl";
                    const otherInputId = `${baseId}_${otherLocale}`;
                    const button = createTranslationButton(buttonText, otherInputId, elements[baseId], toastDangerMessage, targetLocale);
                    button.classList.add('translation-button');
                    label.appendChild(button);
                    label.classList.add("flex", "gap-2");
                }
            }
        });
    });
}
