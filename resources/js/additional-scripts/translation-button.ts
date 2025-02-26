// @ts-ignore
import { compareArrays, getHostname, getTokenCSRF } from "../helpers";
// @ts-ignore
import Toast from "./toast";

class TranslationButton {
    private text: string;
    private elementIdWithLocale: string;
    private elements: { en: HTMLInputElement | HTMLTextAreaElement | null, pl: HTMLInputElement | HTMLTextAreaElement | null };
    private toastDangerMessage: string;
    private targetLocale: string;
    private lastSourceText: string = "";
    private hasBeenTranslated: boolean = false;

    constructor(text: string, elementIdWithLocale: string, elements: { en: HTMLInputElement | HTMLTextAreaElement | null, pl: HTMLInputElement | HTMLTextAreaElement | null }, toastDangerMessage: string, targetLocale: string) {
        this.text = text;
        this.elementIdWithLocale = elementIdWithLocale;
        this.elements = elements;
        this.toastDangerMessage = toastDangerMessage;
        this.targetLocale = targetLocale;
    }

    public initializeButtonElement(): HTMLButtonElement {
        const button = this.createButtonElement();
        button.id = this.generateRandomId("translation-button");
        button.addEventListener("click", (event) => this.handleTranslate(event));

        this.updateButtonState(button);

        const { sourceLocale } = this.findValueAndSourceLocaleToTranslate();
        const sourceInput = this.findInputBySourceLocale(sourceLocale);

        if (sourceInput) {
            this.updateButtonState(button);
            sourceInput.addEventListener('input', () => this.updateButtonState(button));
        }

        return button;
    }

    private updateButtonState(button: HTMLButtonElement): void {
        const { sourceLocale } = this.findValueAndSourceLocaleToTranslate();
        const sourceInput = this.findInputBySourceLocale(sourceLocale); // Znajdujemy input na podstawie sourceLocale

        if (sourceInput) {
            const isEmpty = sourceInput.value.trim() === ''; // Sprawdzamy, czy input jest pusty
            const isTextChanged = sourceInput.value !== this.lastSourceText; // Sprawdzamy, czy tekst się zmienił

            const shouldBeDisabled = isEmpty || (this.hasBeenTranslated && !isTextChanged);

            button.disabled = shouldBeDisabled;

            if (shouldBeDisabled) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.classList.remove('hover:bg-primary-600', 'hover:text-white');
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.classList.add('hover:bg-primary-600', 'hover:text-white');
            }
        }
    }

    private findInputBySourceLocale(sourceLocale: string): HTMLInputElement | HTMLTextAreaElement | null {
        return this.elements[sourceLocale as keyof typeof this.elements];
    }

    private generateRandomId(name: string) {
        const timestamp = new Date().getTime();
        const randomNumber = Math.floor(Math.random() * 1000000);
        const id = `${name}-${timestamp}${randomNumber}`;
        return id;
    }

    private createLoaderElement(): HTMLElement {
        const loader = document.createElement("div");
        loader.classList.add("spinner-form");
        return loader;
    }

    private createIconElement(): string {
        return `<svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-sparkles"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 18a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm0 -12a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm-7 12a6 6 0 0 1 6 -6a6 6 0 0 1 -6 -6a6 6 0 0 1 -6 6a6 6 0 0 1 6 6z" /></svg>`;
    }

    private createButtonElement(): HTMLButtonElement {
        const button: HTMLButtonElement = document.createElement("button");
        button.innerHTML = `${this.text} ${this.createIconElement()}`;
        button.type = "button";
        button.classList.add(
            "text-xs",
            "border-2",
            "rounded-md",
            "px-2",
            "flex",
            "gap-1",
            "border-primary-900",
            "hover:bg-primary-600",
            "hover:text-white",
            "transition-all",
            "duration-300"
        );
        return button;
    }

    private isItemLanguageSibling(item: Element, elementsIdAsArray: string[]): boolean {
        const id = item.id;
        const itemIdArray = id.split("_");
        return elementsIdAsArray.slice(0, -1).every((part, index) => part === itemIdArray[index]);
    }

    private findValueAndSourceLocaleToTranslate() {
        let value: string | null = null;
        let sourceLocale: string = "";
        Object.values(this.elements).forEach((item) => {
            if (item && this.isItemLanguageSibling(item, this.elementIdWithLocale.split("_"))) {
                value = (item as HTMLInputElement | HTMLTextAreaElement).value;
                const localeArray = (item as HTMLInputElement | HTMLTextAreaElement).id.split("_");
                sourceLocale = localeArray[localeArray.length - 1];
            }
        });

        return { value, sourceLocale };
    }

    private async fetchTranslation(text: string, from: string, to: string) {
        const url = getHostname() + "/admin/translate";
        console.log(url,text,from,to);
        return await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": getTokenCSRF()
            },
            body: JSON.stringify({
                text,
                from,
                to
            })
        });
    }

    // @ts-ignore
    private async handleTranslate(event: MouseEvent) {
        const { value, sourceLocale } = this.findValueAndSourceLocaleToTranslate();
        const currentElementInput = this.elements[this.targetLocale as keyof typeof this.elements];
        const loader = this.createLoaderElement();
        const button = event.target as HTMLButtonElement;

        try {
            button.appendChild(loader);
            button.disabled = true;
            const response = await this.fetchTranslation(value ?? "", sourceLocale, this.targetLocale);
            if (!response.ok) {
                throw new Error("Something went wrong");
            }
            const translation = await response.json();
            if (currentElementInput) {
                (currentElementInput as HTMLInputElement | HTMLTextAreaElement).value = translation.result;
            }

            this.hasBeenTranslated = true;
            this.lastSourceText = value ?? "";

        } catch (e) {
            Toast.danger(this.toastDangerMessage);
            this.hasBeenTranslated = false;
            this.lastSourceText = value ?? "";

        } finally {
            button.removeChild(loader);
            this.updateButtonState(button);
        }
    }
}

const createTranslationButton = (text: string, elementIdWithLocale: string, elements: { en: HTMLInputElement | HTMLTextAreaElement | null, pl: HTMLInputElement | HTMLTextAreaElement | null }, toastDangerMessage: string, targetLocale: string): HTMLButtonElement => {
    const button = new TranslationButton(text, elementIdWithLocale, elements, toastDangerMessage, targetLocale);
    return button.initializeButtonElement();
};

export default createTranslationButton;
