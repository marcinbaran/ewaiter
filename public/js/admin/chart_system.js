function clearData(chart) {
    chart.data.labels = [];
    chart.data.datasets.forEach((dataset) => {
        dataset.data = [];
    });
    chart.update();
}

$(document).ready(function () {
    $("#idForm2").on("submit", function () {
        var tabl = [];
        var dataSet = [];
        $.get(
            "/admin/dashboard/restaurants",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
            },
            function (json, status) {
                $.each(json.results.result, function (i, v) {
                    var t = [];
                    $.each(v, function (j, w) {
                        t.push(w);
                    });
                    dataSet.push(t);
                });
                columnsData = [];
                $.each(json.results.columns, function (i, v) {
                    var obj = {};
                    obj.title = v;
                    columnsData.push(obj);
                });
                if ($.fn.dataTable.isDataTable("#tableOrders")) {
                    $("#tableOrders").DataTable().destroy();
                    $("#tableOrders").empty();
                }
                if (dataSet.length > 0) {
                    tabl["tableOrders"] = $("#tableOrders").DataTable({
                        data: dataSet,
                        columns: columnsData,
                        scrollX: true,
                        language: {
                            url: "/js/admin/Polish.json",
                        },
                    });
                }
            },
            "json",
        );

        return false;
    });

    $("#createAtStart,#createAtEnd").datepicker({
        onHide: function () {
            $("#idForm2").submit();
        },
    });
    $("#idForm2").submit();
});
