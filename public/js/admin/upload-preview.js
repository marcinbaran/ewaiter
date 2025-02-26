(function ($) {
    $.extend({
        uploadPreview: function (options) {
            // Options + Defaults
            var settings = $.extend(
                {
                    input_field: ".image-input",
                    preview_box: ".image-preview",
                    label_field: ".image-label",
                    label_default: "Choose File",
                    label_selected: "Change File",
                    no_label: false,
                    success_callback: null,
                    max_size: null,
                },
                options,
            );

            checkFilesSize = function (elem) {
                var nBytes = 0;
                var oFiles = $(elem).get(0);
                if (!oFiles.files || oFiles.files.length < 1) {
                    return nBytes;
                }
                var nFiles = oFiles.files.length;
                for (var nFileId = 0; nFileId < nFiles; nFileId++) {
                    nBytes += oFiles.files[nFileId].size;
                }
                return nBytes;
            };

            formatBytes = function (nBytes) {
                var sOutput = nBytes + " bytes";
                // optional code for multiples approximation
                for (
                    var aMultiples = ["K", "M", "G", "T", "P", "E", "Z", "Y"],
                        nMultiple = 0,
                        nApprox = nBytes / 1024;
                    nApprox > 1;
                    nApprox /= 1024, nMultiple++
                ) {
                    var r0 = nApprox.toFixed(0);
                    var r3 = nApprox.toFixed(3);
                    sOutput = (r3 / r0 == 1 ? r0 : r3) + aMultiples[nMultiple];
                }
                return sOutput;
            };

            // Check if FileReader is available
            if (window.File && window.FileList && window.FileReader) {
                if (
                    typeof $(settings.input_field) !== "undefined" &&
                    $(settings.input_field) !== null
                ) {
                    $(settings.input_field).change(function () {
                        var files = this.files;
                        var the = this;

                        if (files.length > 0) {
                            var file = files[0];
                            var reader = new FileReader();

                            // Load file
                            reader.addEventListener("load", function (event) {
                                if (settings.max_size !== null) {
                                    fileSize = checkFilesSize(the);
                                    if (fileSize > settings.max_size) {
                                        alert(
                                            "The file is too big (" +
                                                formatBytes(fileSize) +
                                                "). It must be < " +
                                                formatBytes(settings.max_size),
                                        );
                                        this.abort();
                                        $(the).val(null).trigger("invalid");
                                        return false;
                                    }
                                }

                                var loadedFile = event.target;

                                // Check format
                                if (file.type.match("image")) {
                                    // Image

                                    if (
                                        settings.preview_box.prop(
                                            "nodeName",
                                        ) === "IMG"
                                    ) {
                                        $(settings.preview_box).attr(
                                            "src",
                                            loadedFile.result,
                                        );
                                    } else {
                                        $(settings.preview_box).css(
                                            "background-image",
                                            "url(" + loadedFile.result + ")",
                                        );
                                    }
                                } else if (file.type.match("audio")) {
                                    // Audio
                                    $(settings.preview_box).html(
                                        "<audio controls><source src='" +
                                            loadedFile.result +
                                            "' type='" +
                                            file.type +
                                            "' />Your browser does not support the audio element.</audio>",
                                    );
                                } else {
                                    alert(
                                        "This file type is not supported yet.",
                                    );
                                }
                            });

                            if (settings.no_label == false) {
                                // Change label
                                $(settings.label_field).html(
                                    settings.label_selected,
                                );
                            }

                            // Read the file
                            reader.readAsDataURL(file);

                            // Success callback function call
                            if (settings.success_callback) {
                                settings.success_callback();
                            }
                        } else {
                            if (settings.no_label == false) {
                                // Change label
                                $(settings.label_field).html(
                                    settings.label_default,
                                );
                            }

                            // Clear background
                            $(settings.preview_box).css(
                                "background-image",
                                "none",
                            );

                            // Remove Audio
                            $(settings.preview_box + " audio").remove();
                        }
                    });
                }
            } else {
                alert(
                    "You need a browser with file reader support, to use this form properly.",
                );
                return false;
            }
            // init callback function call
            if (settings.init) {
                settings.init();
            }
        },
    });
})(jQuery);
