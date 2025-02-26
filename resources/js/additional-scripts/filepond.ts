import * as FilePond from "filepond";
import "filepond/dist/filepond.min.css";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css";
import FilePondPluginImageEdit from "filepond-plugin-image-edit";
import "filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImageValidateSize from "filepond-plugin-image-validate-size";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";

import pl_PL from "filepond/locale/pl-pl.js";

import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";
import { getTranslation } from "./i18n";

FilePond.registerPlugin(FilePondPluginImagePreview);
FilePond.registerPlugin(FilePondPluginImageEdit);
FilePond.registerPlugin(FilePondPluginFileValidateType);
FilePond.registerPlugin(FilePondPluginImageValidateSize);
FilePond.registerPlugin(FilePondPluginFileValidateSize);

const icons = {
    edit: '<div class="rounded-full bg-gray-800 text-white p-1 aspect-square flex justify-center items-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 icon icon-tabler icon-tabler-photo-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 8h.01"></path><path d="M11 20h-4a3 3 0 0 1 -3 -3v-10a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v4"></path><path d="M4 15l4 -4c.928 -.893 2.072 -.893 3 0l3 3"></path><path d="M14 14l1 -1c.31 -.298 .644 -.497 .987 -.596"></path><path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z"></path></svg></div>',
    confirm:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>',
    discard:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>',
    rotateLeft:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-rotate-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 4.55a8 8 0 0 0 -6 14.9m0 -4.45v5h-5"></path><path d="M18.37 7.16l0 .01"></path><path d="M13 19.94l0 .01"></path><path d="M16.84 18.37l0 .01"></path><path d="M19.37 15.1l0 .01"></path><path d="M19.94 11l0 .01"></path></svg>',
    rotateRight:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-rotate-clockwise-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 4.55a8 8 0 0 1 6 14.9m0 -4.45v5h5"></path><path d="M5.63 7.16l0 .01"></path><path d="M4.06 11l0 .01"></path><path d="M4.63 15.1l0 .01"></path><path d="M7.16 18.37l0 .01"></path><path d="M11 19.94l0 .01"></path></svg>',
    flipHorizontal:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-flip-horizontal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12l18 0"></path><path d="M7 16l10 0l-10 5l0 -5"></path><path d="M7 8l10 0l-10 -5l0 5"></path></svg>',
    flipVertical:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-flip-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 3l0 18"></path><path d="M16 7l0 10l5 0l-5 -10"></path><path d="M8 7l0 10l-5 0l5 -10"></path></svg>',
    zoomIn: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-zoom-in" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M7 10l6 0"></path><path d="M10 7l0 6"></path><path d="M21 21l-6 -6"></path></svg>',
    zoomOut:
        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-zoom-out" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M7 10l6 0"></path><path d="M21 21l-6 -6"></path></svg>',
};

class ImageEditor {
    editor: {
        open: (file: any, instructions: any) => void;
        onconfirm: (output: any) => void;
        oncancel: () => void;
        onclose: () => void;
    };
    cropper: Cropper;
    wrapper: HTMLDivElement;
    file: any;
    image: HTMLImageElement;
    filepond: any;
    aspectRatio: "rectangle" | "square";

    constructor(filepond, aspectRatio) {
        this.editor = {
            open: (file, instructions) => {
                this.open(file, instructions);
            },
            onconfirm: (output) => {},
            oncancel: () => {},
            onclose: () => {},
        };
        this.filepond = filepond;
        this.filepond.setOptions({
            imageEditEditor: this.editor,
            imageEditIconEdit: icons.edit,
        });
        this.aspectRatio = aspectRatio;
    }

    open(file, instructions) {
        this.file = file;
        const rotateLeftButton = this.#createButton(icons.rotateLeft, () => {
            this.cropper.rotate(-90);
        });
        const rotateRightButton = this.#createButton(icons.rotateRight, () => {
            this.cropper.rotate(90);
        });
        const flipHorizontalButton = this.#createButton(
            icons.flipHorizontal,
            () => {
                this.cropper.scaleY(-this.cropper.getData().scaleY);
            },
        );
        const flipVerticalButton = this.#createButton(
            icons.flipVertical,
            () => {
                this.cropper.scaleX(-this.cropper.getData().scaleX);
            },
        );
        const zoomInButton = this.#createButton(icons.zoomIn, () => {
            this.cropper.zoom(0.1);
        });
        const zoomOutButton = this.#createButton(icons.zoomOut, () => {
            this.cropper.zoom(-0.1);
        });
        const separator = this.#createSeparator();
        const confirmButton = this.#createButton(icons.confirm, this.confirm);
        const cancelButton = this.#createButton(icons.discard, this.cancel);

        const buttons = this.#createButtonContainer([
            rotateLeftButton,
            rotateRightButton,
            flipHorizontalButton,
            flipVerticalButton,
            zoomInButton,
            zoomOutButton,
            separator,
            confirmButton,
            cancelButton,
        ]);

        this.image = this.#createImageFromFile(file);

        const imageWrapper = this.#createImageWrapper(this.image);

        const editor = this.#createEditor([imageWrapper, buttons]);
        const backdrop = this.#createBackdrop();

        this.wrapper = this.#createWrapper([backdrop, editor]);

        document.body.appendChild(this.wrapper);

        this.cropper = new Cropper(this.image, {
            aspectRatio: this.aspectRatio == "rectangle" ? 16 / 9 : 1 / 1,
        });
    }

    async confirm() {
        this.image.src = this.cropper.getCroppedCanvas().toDataURL();
        this.cropper.destroy();
        this.wrapper.remove();

        console.log(this.filepond.getFiles(), this.file, this.image);

        this.#createBlobFromImage(this.image, this.file.name)
            .then((imageBlob) => {
                this.filepond.getFiles().forEach((file) => {
                    if (this.file === file.file) {
                        this.filepond.removeFile(file.id, {
                            revert: true,
                        });
                    }
                });
                this.filepond.addFile(imageBlob).then((file) => {
                    console.log(file, this.filepond.getFiles());
                });
            })
            .catch((error) => {
                console.error("Error converting image to File:", error);
            });
    }

    cancel() {
        this.cropper.destroy();
        this.wrapper.remove();
        this.editor.oncancel();
    }

    close() {
        this.cropper.destroy();
        this.wrapper.remove();
        this.editor.onclose();
    }

    #createButton(icon: string, action: Function): HTMLButtonElement {
        const button = document.createElement("button");
        button.className =
            "cropper-confirm-button rounded-full bg-primary-500 p-2 aspect-square hover:bg-primary-300 dark:hover:bg-primary-700";
        button.innerHTML = icon;
        button.addEventListener("click", () => {
            action.bind(this)();
        });
        return button;
    }

    #createButtonContainer(
        buttons: (HTMLButtonElement | HTMLDivElement)[],
    ): HTMLDivElement {
        const container = document.createElement("div");
        container.className =
            "cropper-buttons w-full flex justify-center items-center gap-5";
        buttons.forEach((button) => container.appendChild(button));
        return container;
    }

    #createSeparator(): HTMLDivElement {
        const separator = document.createElement("div");
        separator.className = "cropper-separator grow";
        return separator;
    }

    #createImageFromFile(file: File): HTMLImageElement {
        const image = new Image();
        image.src = URL.createObjectURL(file);
        return image;
    }

    #createWrapper(children: HTMLElement[]): HTMLDivElement {
        const wrapper = document.createElement("div");
        wrapper.className =
            "cropper-wrapper z-50 w-screen h-screen fixed top-0 left-0";
        children.forEach((child) => wrapper.appendChild(child));
        return wrapper;
    }

    #createBackdrop(): HTMLDivElement {
        const backdrop = document.createElement("div");
        backdrop.className =
            "cropper-backdrop w-full h-full absolute top-0 left-0 bg-black opacity-50";
        backdrop.addEventListener("click", () => {
            this.cancel();
        });
        return backdrop;
    }

    #createEditor(children: HTMLElement[]): HTMLDivElement {
        const editor = document.createElement("div");
        editor.className =
            "cropper-editor w-3/4 h-3/4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col justify-center items-center gap-5";
        children.forEach((child) => editor.appendChild(child));
        return editor;
    }

    #createImageWrapper(image: HTMLImageElement): HTMLDivElement {
        const imageWrapper = document.createElement("div");
        imageWrapper.className =
            "cropper-imageWrapper w-full h-full ring ring-primary-500 rounded-lg overflow-hidden";
        imageWrapper.appendChild(image);
        return imageWrapper;
    }

    #createBlobFromImage(
        image: HTMLImageElement,
        fileName: string,
    ): Promise<Blob> {
        const self = this;
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.src = image.src;
            img.onload = function () {
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                canvas.width = img.width;
                canvas.height = img.height;
                context.drawImage(img, 0, 0);
                const dataURL = canvas.toDataURL("image/jpeg");
                const blob = self.#createBlobFromDataUrl(dataURL);
                resolve(blob);
            };
            img.onerror = function () {
                reject(new Error("Error loading the image."));
            };
        });
    }

    #createFileFromBlob(blob: Blob, fileName: string): File {
        return new File([blob], fileName, { type: blob.type });
    }

    #createBlobFromDataUrl(dataUrl: string): Blob {
        const byteString = atob(dataUrl.split(",")[1]);
        const mimeString = dataUrl.split(",")[0].split(":")[1].split(";")[0];
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const uint8Array = new Uint8Array(arrayBuffer);

        for (let i = 0; i < byteString.length; i++) {
            uint8Array[i] = byteString.charCodeAt(i);
        }
        return new Blob([arrayBuffer], { type: mimeString });
    }
}

type FilepondIndicatorAttributes = {
    name: string;
    label: string;
    additionalData: string;
    photos: [];
    processUrl: string;
    token: string;
    revertUrl: string;
    loadUrl: string;
    required: boolean;
    accept: string[];
    aspectRatio: string;
    multiple: boolean;
};

type FilepondInstanceType =
    | "single"
    | "single-required"
    | "multiple"
    | "multiple-required";

class FilepondInstance {
    debug: boolean = false; // Set to true to enable console logs for events

    filepondIndicator: HTMLElement;
    filepondInstance: any;
    type: FilepondInstanceType;
    attributes: object;
    options: object;
    isFileProcessing: boolean = false;
    isFirstFile: boolean;
    submitButtons: HTMLButtonElement[];
    imageEditor: ImageEditor;
    errorParagraph: HTMLParagraphElement;

    constructor(filepondIndicator: HTMLElement, options = {}) {
        this.isFirstFile = true;

        this.filepondIndicator = filepondIndicator;
        this.attributes = this.#getAttributes(filepondIndicator);
        this.options = this.#prepareOptions({ ...this.attributes, ...options });
        this.submitButtons = this.#getSubmitButtons(
            this.#getForm(filepondIndicator)!,
        );
        this.errorParagraph = this.#getErrorParagraph(filepondIndicator)!;
        this.filepondInstance = FilePond.create(
            filepondIndicator,
            this.options,
        );
        this.type = this.#getFilepondInstanceType(
            this.attributes.multiple,
            this.attributes.required,
        );
        this.#setInstanceType(this.type);
        this.#addEventListeners();
        this.imageEditor = new ImageEditor(this.filepondInstance, "rectangle");
    }

    #getAttributes(indicator: HTMLElement): FilepondIndicatorAttributes {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        const attributes = {
            name: indicator.dataset.name ?? "",
            label: indicator.dataset.label ?? "",
            additionalData: indicator.dataset.additionaldata ?? "",
            photos: indicator.dataset.photos ?? "[]",
            processUrl: indicator.dataset.processurl ?? "",
            token: csrfToken ?? "",
            revertUrl: indicator.dataset.reverturl ?? "",
            loadUrl: indicator.dataset.loadurl ?? "",
            required: indicator.dataset.required ?? "false",
            accept: indicator.dataset.accept ?? "",
            aspectRatio: indicator.dataset.aspectratio ?? "",
            multiple: indicator.dataset.multiple ?? "false",
        };
        return {
            ...attributes,
            name: attributes.name?.replace("[]", ""),
            multiple: attributes.multiple === "true",
            label: attributes.label
                ?.replace(
                    "%span_start",
                    '<span class="filepond--label-action">',
                )
                .replace("%span_end", "</span>"),
            photos: JSON.parse(attributes.photos),
            required: attributes.required === "true",
            accept:
                attributes.accept?.split(",").map((type) => type.trim()) ?? [],
        };
    }

    #prepareOptions(
        attributes: FilepondIndicatorAttributes,
        options: object = {},
    ): object {
        const endpoint: string =
            attributes.multiple || (!attributes.multiple && attributes.required)
                ? "multiple"
                : "single";
        return {
            ...pl_PL,
            required: attributes.required,
            allowImagePreview: true,
            labelIdle: attributes.label,
            allowMultiple: attributes.multiple,
            allowReorder: false,
            files: attributes.photos,
            accept: attributes.accept ? attributes.accept : ["image/*"],

            allowImageValidateSize: true,
            imageValidateSizeMinWidth: 100,
            imageValidateSizeMaxWidth: 10000,
            imageValidateSizeMinHeight: 100,
            imageValidateSizeMaxHeight: 10000,

            allowFileSizeValidation: true,
            maxFileSize: "50MB",

            server: {
                process: {
                    url: `${attributes.processUrl}/${attributes.name}/${endpoint}`,
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": attributes.token,
                    },
                    withCredentials: false,
                    onload: (response) => response.key,
                    onerror: (response) => response.data,

                    ondata: (formData) => {
                        formData.append(
                            "additional",
                            attributes.additionalData,
                        );
                        return formData;
                    },
                },
                revert: attributes.revertUrl,
                remove: (source, load, error) => {
                    fetch(attributes.revertUrl, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": attributes.token,
                        },
                        body: JSON.stringify(source),
                    })
                        .then((response) => {
                            load();
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                },
                restore: "./restore/",
                load: `${attributes.loadUrl}/`,
                fetch: "./fetch/",
                headers: {
                    "X-CSRF-TOKEN": attributes.token,
                },
            },
            onreorderfiles: (files) => {
                files.forEach((file, index) => {
                    file.setMetadata("position", index);
                });
            },
            ...options,
        };
    }

    #getForm(target: HTMLElement): HTMLElement | null {
        if (target?.tagName === "FORM") {
            return target;
        } else if (target?.parentElement) {
            return this.#getForm(target.parentElement);
        }

        return null;
    }

    #getErrorParagraph(target: HTMLElement): HTMLParagraphElement {
        return target.parentElement!.querySelector("p.error-message")!;
    }

    #getSubmitButtons(form: HTMLElement): HTMLButtonElement[] {
        return [
            ...form.querySelectorAll('button[type="submit"]'),
        ] as HTMLButtonElement[];
    }

    #getFilepondInstanceType = (
        isMultiple: boolean,
        isRequired: boolean,
    ): FilepondInstanceType => {
        if (!isMultiple && isRequired) {
            return "single-required";
        } else if (!isMultiple && !isRequired) {
            return "single";
        } else if (isMultiple && isRequired) {
            return "multiple-required";
        } else {
            return "multiple";
        }
    };

    #setInstanceType(type: FilepondInstanceType): void {
        if (type === "single-required") {
            this.filepondInstance.setOptions({
                allowMultiple: true,
                maxFiles: 2,
                allowReplace: false,
            });
        } else if (type === "single") {
            this.filepondInstance.setOptions({
                allowMultiple: false,
                maxFiles: 1,
                allowReplace: true,
            });
        } else {
            this.filepondInstance.setOptions({
                allowMultiple: true,
                allowReplace: false,
            });
        }
    }

    #setGalleryError = (errorMessage: string) => {
        if (errorMessage !== "") {
            this.filepondInstance.element.classList.add(
                "ring-2",
                "ring-red-600",
            );
            this.errorParagraph.innerHTML = errorMessage;
        } else {
            this.filepondInstance.element.classList.remove(
                "ring-2",
                "ring-red-600",
            );
            this.errorParagraph.innerHTML = "";
        }
    };

    #changeFormButtonsDisabledState = (isDisabled: boolean) => {
        this.submitButtons.forEach(
            (submitButton) => (submitButton.disabled = isDisabled),
        );
    };

    #changeRevertItemButtonVisibility(isVisible: boolean) {
        const actionButtons = this.filepondInstance.element.querySelectorAll(
            "button.filepond--file-action-button.filepond--action-revert-item-processing",
        );
        actionButtons.forEach((actionButton) => {
            actionButton.classList.toggle("hidden", !isVisible);
        });
        const actionButtonSubsets =
            this.filepondInstance.element.querySelectorAll(
                "span.filepond--file-status-sub",
            );
        actionButtonSubsets.forEach((actionButtonSubset) => {
            actionButtonSubset.classList.toggle("hidden", !isVisible);
        });
    }

    #checkifAtLeastOneFile = () => {
        const files = this.filepondInstance.getFiles();
        if (files.length > 1) {
            this.#changeRevertItemButtonVisibility(true);
            this.#setGalleryError(getTranslation("only_one_file_is_required"));
            this.#changeFormButtonsDisabledState(true);
        } else if (files.length === 1) {
            this.#changeRevertItemButtonVisibility(false);
            this.#setGalleryError("");
            this.#changeFormButtonsDisabledState(false);
        } else {
            this.#changeRevertItemButtonVisibility(false);
            this.#setGalleryError(
                getTranslation("at_least_one_file_is_required"),
            );
            this.#changeFormButtonsDisabledState(true);
        }
    };

    #setFileProcessingState(isFileProcessing: boolean): void {
        if (isFileProcessing) {
            this.isFileProcessing = true;
            this.#changeFormButtonsDisabledState(true);
            this.#setGalleryError(getTranslation("file_is_being_processed"));
        } else {
            this.isFileProcessing = false;
            this.#changeFormButtonsDisabledState(false);
            this.#setGalleryError("");
        }
    }

    #addEventListeners(): void {
        this.#addOnInitListener();
        this.#addOnWarningListener();
        this.#addOnErrorListener();
        this.#addOnInitFileListener();
        this.#addOnAddFileStartListener();
        this.#addOnAddFileProgressListener();
        this.#addOnAddFileListener();
        this.#addOnProcessFileStartListener();
        this.#addOnProcessFileProgressListener();
        this.#addOnProcessFileAbortListener();
        this.#addOnProcessFileRevertListener();
        this.#addOnProcessFileListener();
        this.#addOnRemoveFileListener();
        this.#addOnPrepareFileListener();
        this.#addOnUpdateFilesListener();
        this.#addOnActivateFileListener();
        this.#addOnReorderFilesListener();
    }

    #addOnInitListener(): void {
        this.filepondInstance.on("init", (...data) => {
            if (this.debug) {
                console.log("#init --- START");
                console.log(data);
                console.log("#init --- END");
            }

            this.#setFileProcessingState(false);
            if (this.type === "multiple-required") {
                this.#changeFormButtonsDisabledState(true);
            } else if (this.type === "single-required") {
                this.#checkifAtLeastOneFile();
            } else {
                this.#changeFormButtonsDisabledState(false);
            }
        });
    }

    #addOnWarningListener(): void {
        this.filepondInstance.on("warning", (...data) => {
            if (this.debug) {
                console.log("#warning --- START");
                console.log(data);
                console.log("#warning --- END");
            }
        });
    }

    #addOnErrorListener(): void {
        this.filepondInstance.on("error", (...data) => {
            if (this.debug) {
                console.log("#error --- START");
                console.log(data);
                console.log("#error --- END");
            }

            this.#setFileProcessingState(false);
            if (
                this.type === "single-required" ||
                this.type === "multiple-required"
            ) {
                this.#changeFormButtonsDisabledState(true);
            }
            this.#setGalleryError(getTranslation("file_error"));
        });
    }

    #addOnInitFileListener(): void {
        this.filepondInstance.on("initfile", (...data) => {
            if (this.debug) {
                console.log("#initfile --- START");
                console.log(data);
                console.log("#initfile --- END");
            }
            this.#setFileProcessingState(true);
        });
    }

    #addOnAddFileStartListener(): void {
        this.filepondInstance.on("addfilestart", (...data) => {
            if (this.debug) {
                console.log("#addfilestart --- START");
                console.log(data);
                console.log("#addfilestart --- END");
            }
        });
    }

    #addOnAddFileProgressListener(): void {
        this.filepondInstance.on("addfileprogress", (...data) => {
            if (this.debug) {
                console.log("#addfileprogress --- START");
                console.log(data);
                console.log("#addfileprogress --- END");
            }
        });
    }

    #addOnAddFileListener(): void {
        this.filepondInstance.on("addfile", (...data) => {
            if (this.debug) {
                console.log("#addfile --- START");
                console.log(data);
                console.log("#addfile --- END");
            }
        });
    }

    #addOnProcessFileStartListener(): void {
        this.filepondInstance.on("processfilestart", (...data) => {
            if (this.debug) {
                console.log("#processfilestart --- START");
                console.log(data);
                console.log("#processfilestart --- END");
            }
        });
    }

    #addOnProcessFileProgressListener(): void {
        this.filepondInstance.on("processfileprogress", (...data) => {
            if (this.debug) {
                console.log("#processfileprogress --- START");
                console.log(data);
                console.log("#processfileprogress --- END");
            }
        });
    }

    #addOnProcessFileAbortListener(): void {
        this.filepondInstance.on("processfileabort", (...data) => {
            if (this.debug) {
                console.log("#processfileabort --- START");
                console.log(data);
                console.log("#processfileabort --- END");
            }
        });
    }

    #addOnProcessFileRevertListener(): void {
        this.filepondInstance.on("processfilerevert", (...data) => {
            if (this.debug) {
                console.log("#processfilerevert --- START");
                console.log(data);
                console.log("#processfilerevert --- END");
            }
        });
    }

    #addOnProcessFileListener(): void {
        this.filepondInstance.on("processfile", (...data) => {
            if (this.debug) {
                console.log("#processfile --- START");
                console.log(data);
                console.log("#processfile --- END");
            }

            this.#setFileProcessingState(false);
            if (this.type === "single-required") {
                this.#checkifAtLeastOneFile();
            }
        });
    }

    #addOnRemoveFileListener(): void {
        this.filepondInstance.on("removefile", (...data) => {
            if (this.debug) {
                console.log("#removefile --- START");
                console.log(data);
                console.log("#removefile --- END");
            }
        });
    }

    #addOnPrepareFileListener(): void {
        this.filepondInstance.on("preparefile", (...data) => {
            if (this.debug) {
                console.log("#preparefile --- START");
                console.log(data);
                console.log("#preparefile --- END");
            }
        });
    }

    #addOnUpdateFilesListener(): void {
        this.filepondInstance.on("updatefiles", (...data) => {
            if (this.debug) {
                console.log("#updatefiles --- START");
                console.log(data);
                console.log("#updatefiles --- END");
            }

            const isBadError = data[0].some((err) => err.status === 8);
            if (this.type === "single-required" && !this.isFileProcessing) {
                if (isBadError) {
                    this.#changeFormButtonsDisabledState(true);
                } else {
                    this.#checkifAtLeastOneFile();
                }
            }
        });
    }

    #addOnActivateFileListener(): void {
        this.filepondInstance.on("activatefile", (...data) => {
            if (this.debug) {
                console.log("#activatefile --- START");
                console.log(data);
                console.log("#activatefile --- END");
            }
        });
    }

    #addOnReorderFilesListener(): void {
        this.filepondInstance.on("reorderfiles", (...data) => {
            if (this.debug) {
                console.log("#reorderfiles --- START");
                console.log(data);
                console.log("#reorderfiles --- END");
            }
        });
    }
}

const main = (): void => {
    const filepondIndicators = document.querySelectorAll(".fileupload");
    filepondIndicators.forEach((filepondIndicator) => {
        const filepondInstance = new FilepondInstance(
            filepondIndicator as HTMLElement,
            {},
        );
    });
};
window.addEventListener("initFilepond", () => {
    main();
});

main();
