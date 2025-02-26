const initSelect2 = () => {
    $("select[data-s2]").each(function (index, element) {
        let options = {};
        const select = $(element);

        if (select.data("xhrUrl")) {
            options.ajax = {
                url: select.data("xhrUrl"),
                dataType: "json",
                processResults: function (data) {
                    return {
                        results: data.results,
                    };
                },
                data: function (params) {
                    return {
                        query_phrase: params.term,
                        query_type: "select2",
                    };
                },
            };
        }
        $(element).select2(options);
    });
};

window.addEventListener("initSelect2", () => {
    initSelect2();
});

initSelect2();
