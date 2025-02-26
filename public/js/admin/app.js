Array.prototype.diff = function (a) {
    return this.filter(function (i) {
        return a.indexOf(i) === -1;
    });
};

Array.prototype.max = function () {
    return Math.max.apply(null, this);
};

Array.prototype.min = function () {
    return Math.min.apply(null, this);
};

Array.prototype.sum = function () {
    return this.reduce(function (a, b) {
        return a + b;
    }, 0);
};
Array.prototype.getColumn = function (name) {
    return this.map(function (el) {
        // gets corresponding 'column'
        if (el.hasOwnProperty(name)) return el[name];
        // removes undefined values
    }).filter(function (el) {
        return typeof el != "undefined";
    });
};
String.prototype.ucfirst = function () {
    return this.charAt(0).toUpperCase() + this.substr(1);
};
Storage.prototype.setObj = function (key, obj) {
    return this.setItem(key, JSON.stringify(obj));
};
Storage.prototype.getObj = function (key) {
    return JSON.parse(this.getItem(key));
};
$(document).ready(function () {
    jQuery(".select").select2().removeClass("invisible");
    // if (typeof bootbox!=="undefined") {
    //     bootbox.setDefaults({
    //         locale: $('html').attr('lang')
    //     });
    // }
    const notificationMenu = document.querySelector(
        ".navbar-nav > .notifications-menu > .dropdown-menu > li:not(.header)",
    );
    if (notificationMenu) {
        const scrollbar_notification = new PerfectScrollbar(
            ".navbar-nav > .notifications-menu > .dropdown-menu > li:not(.header)",
            {},
        );
    }
    const scrollbar_navbar = new PerfectScrollbar(
        "#mainNav.fixed-top .navbar-sidenav",
        {},
    );

    $("[data-tablang]").click(function () {
        const tablang = $(this).data("tablang");
        $('[data-tablang="' + tablang + '"]').tab("show");
    });
});
function enableLoaders(selector) {
    $(selector).submit(function () {
        let valid = true; // TODO: Polaczyc z walidacja
        if (valid) {
            $(this).submit(function () {
                return false;
            });
            $(this).find('*[type="submit"] .spinner-border').remove();
            $(this)
                .find('*[type="submit"]')
                .prepend(
                    '<span style=" margin-right: 5px; position: relative; top: -2px;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>',
                );

            return true;
        } else {
            return false;
        }
    });
}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

$(document).ready(function () {
    const LangValidation = {
        arrayOfLangs: [],

        init: function () {
            const tabsOnPage = $("[data-tablang]");

            //pobiera liste jezykow
            let arrayOfLangs = [];
            if (tabsOnPage.length) {
                for (let i = 0; i < tabsOnPage.length; i++) {
                    const tab = $(tabsOnPage[i]);
                    arrayOfLangs.push(tab.data("tablang"));
                }
                arrayOfLangs = arrayOfLangs.filter(onlyUnique);
            }
            this.arrayOfLangs = arrayOfLangs;
            //ustawia event na kazdy input w tabach z jezykami
            if (tabsOnPage.length) {
                for (let i = 0; i < tabsOnPage.length; i++) {
                    const tab = $(tabsOnPage[i]);
                    const target = $(tab.attr("href"));
                    const allInputsInTarget = target.find("input, textarea");

                    this.setEventsOnListOfInputs(allInputsInTarget);
                }
            }
        },
        setEventsOnListOfInputs: function (listOfInputs) {
            if (listOfInputs.length) {
                for (let i = 0; i < listOfInputs.length; i++) {
                    const input = $(listOfInputs[i]);
                    this.setValidationEventOnInput(input);
                }
            }
        },
        setValidationEventOnInput: function (input) {
            const context = this;

            input.on("input", function (e) {
                const text = $(this).val();

                if (text.length > 0) {
                    context.addRequired($(this));
                } else {
                    context.removeRequired($(this));
                }
            });
        },
        getLanguageVariantsOfInput(input) {
            const idOfInput = "dish_description_pl".split("_");
            const langOfInput = idOfInput.pop();
            const mainPartOfId = idOfInput.join("_");

            let translationVariants = [];
            //            for(let i=0; i<this.arrayOfLangs.length; i++){
            //                const lang = this.arrayOfLangs[i];
            const lang = "pl";
            const inputSelector = `#${mainPartOfId}_${lang}`;
            translationVariants.push($(inputSelector));

            //            }
            return translationVariants;
        },
        addRequired: function (input) {
            const listOfVariants = this.getLanguageVariantsOfInput(input);
            for (let i = 0; i < listOfVariants.length; i++) {
                const input = listOfVariants[i];
                const validatedBefore = input.data("addedbyvalidation");
                input.attr("required", "required");
                //                if(input.prop('required') && !validatedBefore){
                //
                //                }
            }
        },
        removeRequired: function (input) {
            const listOfVariants = this.getLanguageVariantsOfInput(input);
            for (let i = 0; i < listOfVariants.length; i++) {
                const input = listOfVariants[i];
                const validatedBefore = input.data("addedbyvalidation");
                input.removeAttr("required");
            }
        },
    };

    LangValidation.init();
});

$(document).ready(function () {
    const DatePickerSynch = {
        picker: null,
        mode: null,
        synchPicker: null,

        init: function (picker, mode) {
            this.picker = picker;
            this.mode = mode;

            this.setSynchPicker();

            this.setEvents();
        },
        setEvents: function () {
            const self = this;
            $(this.picker).change(function () {
                const pickedDate = new Date(Date.parse($(self.picker).val()));
                console.log(self.synchPicker);
                if (self.mode == "greater") {
                    $(self.synchPicker).datepicker({
                        minDate: pickedDate,
                    });
                } else {
                    $(self.synchPicker).datepicker({
                        maxDate: pickedDate,
                    });
                }
            });
        },
        setSynchPicker: function () {
            if (this.mode == "greater") {
                this.synchPicker = $($(this.picker).data("greaterThan"));
                //                console.log($(this.picker).data('greaterThan'));
            } else {
                this.synchPicker = $($(this.picker).data("lessThan"));
                //                console.log($(this.picker).data('lessThan'));
            }
        },
    };
    const pickersGreaterThan = $("[data-greater-than]");
    const pickersLessThan = $("[data-less-than]");

    for (let i = 0; i < pickersGreaterThan.length; i++) {
        let picker = pickersGreaterThan[i];
        let mode = "greater";

        let pickerSynchGreater = Object.create(DatePickerSynch);
        pickerSynchGreater.init(picker, mode);
    }
    for (let i = 0; i < pickersLessThan.length; i++) {
        let picker = pickersLessThan[i];
        let mode = "lower";

        let pickerSynchLower = Object.create(DatePickerSynch);
        pickerSynchLower.init(picker, mode);
    }
});
