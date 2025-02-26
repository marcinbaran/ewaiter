class SlugifyInput {
    inputElement: HTMLInputElement;
    outputElement: HTMLInputElement;
    inputText: string;
    outputText: string;

    static slugifyInputClass = "input-slug";
    static slugifyOutputAttribute = "data-slug-for";

    constructor(outputElement: HTMLInputElement) {
        this.outputElement = outputElement;
        this.inputElement = this.#findInputElement();
        this.#addEventListenerForInput();
    }

    #findInputElement(): HTMLInputElement {
        if (this.outputElement) {
            const inputElementId: string = this.outputElement.getAttribute(
                SlugifyInput.slugifyOutputAttribute,
            ) as string;
            if (inputElementId) {
                const inputElement: HTMLInputElement | null =
                    document.getElementById(inputElementId) as HTMLInputElement;
                if (inputElement) {
                    return inputElement;
                }
                throw new Error(`No element found with id ${inputElementId}`);
            }
            throw new Error(
                "No data-slug-for attribute found on input element",
            );
        }
        throw new Error("No input element found");
    }

    #updateOutput(): void {
        this.outputText = this.#slugifyText(this.inputText);
        this.outputElement.value = this.outputText;
    }

    #addEventListenerForInput(): void {
        this.inputElement.addEventListener("input", (event: Event) => {
            this.inputText = (event.target as HTMLInputElement).value;
            this.#updateOutput();
        });
    }

    #slugifyText(phrase: string): string {
        return phrase
            .replace(/^\s+|\s+$/g, "") // trim leading/trailing white space
            .toLowerCase() // convert string to lowercase
            .replace(/[^a-z0-9 -]/g, "") // remove any non-alphanumeric characters
            .replace(/\s+/g, "-") // replace spaces with hyphens
            .replace(/-+/g, "-"); // remove consecutive hyphens
    }
}

const main = () => {
    const slugifyInputs: NodeListOf<HTMLInputElement> =
        document.querySelectorAll(`.${SlugifyInput.slugifyInputClass}`);
    slugifyInputs.forEach((outputElement: HTMLInputElement) => {
        new SlugifyInput(outputElement);
    });
};

document.addEventListener("DOMContentLoaded", main);
