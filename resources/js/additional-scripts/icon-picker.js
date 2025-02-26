class IconPicker {
    #attributes = {};
    #icons = [];

    #parent = null;
    #userInterface = null;
    #chosenIcon = null;
    #iconSelector = null;
    #iconSelectorIcons = null;
    #backdrop = null;

    static instances = [];

    constructor(element) {
        if (element) {
            [this.#attributes, this.#icons] =
                this.#getInitialElementValues(element);
            this.#createComponent(element);
            this.#addIconsListeners();
            this.#addChosenIconListener();
            this.#setInitialIcon();
        }
    }

    #getInitialElementValues(element) {
        if (
            !element ||
            element.tagName.toLowerCase() != "select" ||
            !element.classList.contains("icon-picker")
        ) {
            return [null, null];
        }
        const attributes = {
            name: element.name,
            class: element.className,
            id: element.id,
        };
        const icons = [...element.children].map((child) => {
            return {
                path: child.value,
                name: child.title,
                selected: child.selected,
            };
        });
        return [attributes, icons];
    }

    #setInitialIcon = () => {
        const selectedIcon =
            this.#icons.find((icon) => icon.selected) ?? this.#icons[0];
        const chosenIconImg = this.#chosenIcon.querySelector("img");
        chosenIconImg.src = selectedIcon.path;
    };

    #createComponent = (element) => {
        const selectedIcon =
            this.#icons.find((icon) => icon.selected) && this.#icons[0];

        this.#chosenIcon = this.#createChosenIcon(
            selectedIcon.name,
            selectedIcon.path,
        );
        this.#iconSelectorIcons = this.#icons.map((icon) => {
            return this.#createIcon(
                this.#attributes.name,
                icon.path,
                icon.selected,
                icon.name,
                icon.path,
            );
        });
        this.#iconSelector = this.#createSelector(this.#iconSelectorIcons);
        this.#userInterface = this.#createUserInterface(
            this.#chosenIcon,
            this.#iconSelector,
        );
        this.#parent = this.#createParent(this.#userInterface);
        this.#backdrop = this.#createBackdrop(this.#iconSelector);
        this.#backdrop && document.body.appendChild(this.#backdrop);
        element.replaceWith(this.#parent);
    };

    #createParent(...children) {
        const parent = document.createElement("div");
        parent.classList.add("icon-picker");
        children.forEach((child) => {
            parent.appendChild(child);
        });
        return parent;
    }

    #createUserInterface(...children) {
        const userInterface = document.createElement("div");
        userInterface.classList.add("icon-picker--interface");
        children.forEach((child) => {
            userInterface.appendChild(child);
        });
        return userInterface;
    }

    #createChosenIcon(iconTitle, iconPath) {
        const chosenIcon = document.createElement("div");
        chosenIcon.classList.add("icon-picker--chosen-icon");
        chosenIcon.title = iconTitle;
        const img = document.createElement("img");
        img.src = iconPath;
        chosenIcon.appendChild(img);
        return chosenIcon;
    }

    #createSelector(icons) {
        const selector = document.createElement("div");
        selector.classList.add("icon-picker--selector");
        const selectorIcons = document.createElement("div");
        selectorIcons.classList.add("icon-picker--icons");
        icons.forEach((icon) => {
            selectorIcons.appendChild(icon);
        });
        selector.appendChild(selectorIcons);
        return selector;
    }

    #createIcon(name, value, selected, title, iconPath) {
        const icon = document.createElement("label");
        icon.classList.add("icon-picker--icon");
        icon.title = title;

        const input = document.createElement("input");
        input.type = "radio";
        input.name = name;
        input.value = value;
        input.checked = selected;
        input.classList.add("icon-picker--radio");

        const img = document.createElement("img");
        img.src = iconPath;
        img.alt = title;
        img.classList.add("icon-picker--image");

        icon.appendChild(input);
        icon.appendChild(img);
        return icon;
    }

    #createBackdrop(iconSelector) {
        const existingBackdrop = document.querySelector(
            ".icon-picker--backdrop",
        );
        let backdrop = null;
        if (!existingBackdrop) {
            backdrop = document.createElement("div");
            backdrop.classList.add("icon-picker--backdrop");
        } else {
            backdrop = existingBackdrop;
        }
        backdrop.addEventListener("click", (e) => {
            iconSelector.classList.remove("active");
        });
        return existingBackdrop ? null : backdrop;
    }

    #addIconsListeners() {
        const chosenIconImg = this.#chosenIcon.querySelector("img");
        this.#iconSelectorIcons.forEach((icon) => {
            icon.addEventListener("click", (e) => {
                const img = icon.querySelector("img");
                chosenIconImg.src = img.src;
                this.#iconSelector.classList.remove("active");
            });
        });
    }

    #addChosenIconListener() {
        this.#chosenIcon.addEventListener("click", (e) => {
            this.#iconSelector.classList.toggle("active");
        });
    }

    static init(element) {
        if (element) {
            return IconPicker.instances.push(new IconPicker(element));
        }
        return null;
    }
}

const init = () => {
    const iconPickerElements = [...document.querySelectorAll(".icon-picker")];
    iconPickerElements.forEach((element) => IconPicker.init(element));
};

export default {
    init,
};
