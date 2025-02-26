import $ from "jquery";
import select2 from "select2";

select2($);

interface Select2Option {
    id: number | string;
    text: string;
    selected?: boolean;
    disabled?: boolean;
}

interface Select2Options {
    data: Array<Select2Option>;
    multiple: boolean;
    width: "style";
}

interface Select2Attributes {
    dataUrl: string;
    isMultiple: boolean;
    selectedOptionIds: Array<number | string>;
    nullOption?: string;
}

class Select2Instance {
    public id: string;
    public element: HTMLSelectElement;
    public options: Select2Options;
    public attributes: Select2Attributes;

    public constructor(element: HTMLSelectElement) {
        this.element = element;
        this.id = element.id;
        this.options = {
            data: [],
            multiple: false,
            width: "style",
        } as Select2Options;
        this.attributes = {
            dataUrl: "",
            isMultiple: false,
            selectedOptionIds: [],
        } as Select2Attributes;
    }

    public static async createObjectWithOptions(
        element: HTMLSelectElement,
    ): Promise<Select2Instance> {
        const instance = new Select2Instance(element);
        instance.attributes = this.getAttributes(element);
        if ((instance.attributes as Select2Attributes).dataUrl) {
            instance.options.data = await this.fetchData(
                (instance.attributes as Select2Attributes).dataUrl,
            );
        }
        instance.populateDataWithNullOption();
        if ((instance.attributes as Select2Attributes).isMultiple) {
            instance.options.multiple = true;
        }
        instance.initSelect2();
        if (instance.attributes.selectedOptionIds.length > 0) {
            instance.populateDataWithSelections();
        }
        instance.selectOptions();
        document.dispatchEvent(new Event("select2-init"));

        return instance;
    }

    private static getAttributes(
        element: HTMLSelectElement,
    ): Select2Attributes {
        const dataUrl = element.dataset.select2Url;
        const isMultiple = element.dataset.select2Mode === "multiple";
        const selectedOptionIds = element.dataset.select2OldValue
            ? element.dataset.select2OldValue.split(",")
            : [];
        const nullOption = element.dataset.select2NullOption || null;

        delete element.dataset.select2Url;
        delete element.dataset.select2Mode;
        delete element.dataset.select2OldValue;
        delete element.dataset.select2NullOption;

        return { dataUrl, isMultiple, selectedOptionIds, nullOption };
    }

    private static async fetchData(url: string): Promise<Array<Select2Option>> {
        const response = await fetch(url);
        return await response.json();
    }

    private initSelect2() {
        $(`#${this.id}`).select2(this.options);
    }

    private reinitSelect2() {
        $(`#${this.id}`).select2("destroy");
        this.initSelect2();
    }

    private populateDataWithSelections() {
        this.options.data = this.options.data.map((option) => {
            return {
                ...option,
                selected: this.attributes.selectedOptionIds.includes(
                    `${option.id}`,
                ),
            };
        });
        this.reinitSelect2();
    }

    private selectOptions() {
        const selectedOptions = this.options.data.filter(
            (option) => option.selected,
        );
        if (selectedOptions.length === 0) {
            $(this.element).val(null).trigger("change");
        } else {
            const selectedValues = selectedOptions.map((option) => option.id);
            $(this.element).val(selectedValues).trigger("change");
        }
    }

    private populateDataWithNullOption() {
        if (this.attributes.nullOption) {
            this.options.data.unshift({
                id: "",
                text: this.attributes.nullOption,
            });
        }
    }
}

export const init = () => {
    const elements = Array.from(document.querySelectorAll("select.select2"));
    elements.forEach(async (element) => {
        await Select2Instance.createObjectWithOptions(
            element as HTMLSelectElement,
        );
    });
};
