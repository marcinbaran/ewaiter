function addData(chart, label, data, name) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach((dataset) => {
        if (dataset.label == paidLang || dataset.label == noPaidLang) {
            var val = dataset.label == paidLang ? data.paid : data.noPaid;
            dataset.data.push(val.hasOwnProperty(name) ? val[name] : 0);
        } else {
            if (data[dataset.label] !== undefined) {
                dataset.data[$.inArray(label, chart.data.labels)] =
                    data[dataset.label][name];
            }
        }
    });
    chart.update();
}

function setChartData(gData, name, chart) {
    for (var n in gData) {
        addData(chart, n, gData[n], name);
    }
}

function clearData(chart) {
    chart.data.labels = [];
    chart.data.datasets.forEach((dataset) => {
        dataset.data = [];
    });
    chart.update();
}

function setDataFormResponse(data, gName) {
    globalData[gName] = {};
    if (gName == "table3" || gName == "dish3") {
        for (var n = 0; n < data.length; ++n) {
            var name = data[n][gName == "table3" ? "table_name" : "dish_name"];
            if (!globalData[gName].hasOwnProperty(data[n].date_group)) {
                globalData[gName][data[n].date_group] = {
                    [name]: {},
                };
            }
            globalData[gName][data[n].date_group][name] = data[n];
        }
    } else if (gName != "table2" && gName != "dish2") {
        var temp = gName == "table" ? "table_name" : "dish_name";
        for (var i = 0; i < data.length; ++i) {
            if (!globalData[gName].hasOwnProperty(data[i][temp])) {
                globalData[gName][data[i][temp]] = {
                    paid: {},
                    noPaid: {},
                };
            }

            globalData[gName][data[i][temp]][data[i].paid ? "paid" : "noPaid"] =
                data[i];
        }
    } else {
        for (var n = 0; n < data.length; ++n) {
            var name = data[n][gName == "table2" ? "table_name" : "dish_name"];
            if (!globalData[gName].hasOwnProperty(data[n].date_group)) {
                globalData[gName][data[n].date_group] = {
                    [name]: {},
                };
            }
            globalData[gName][data[n].date_group][name] = data[n];
        }
    }
}

function chartList(data, select, val) {
    var dataSet = [],
        tableName = [],
        lb,
        columnsData = [];
    $.each(data, function (i, v) {
        var tab = [];
        if (!select.includes("2") && !select.includes("Delay")) {
            if (val != "quantity_sum") {
                tab.push(
                    i,
                    $.isEmptyObject(v.paid) ? "0 PLN" : v.paid[val] + " PLN",
                    $.isEmptyObject(v.noPaid)
                        ? "0 PLN"
                        : v.noPaid[val] + " PLN",
                );
            }
            tab.push(
                i,
                $.isEmptyObject(v.paid) ? "0" : v.paid[val],
                $.isEmptyObject(v.noPaid) ? "0" : v.noPaid[val],
            );
            dataSet.push(tab);
        } else if (select.includes("Delay")) {
            $.each(v, function (key, vul) {
                lb =
                    vul.table_id != undefined
                        ? chartTable3.data.labels
                        : chartDish3.data.labels;
                var t = [];
                if ($.inArray(key, tableName) < 0) {
                    tableName.push(key);
                    t[0] = key;
                    t[$.inArray(i, lb) + 1] = vul.delay;
                    dataSet.push(t);
                } else {
                    dataSet[$.inArray(key, tableName)][$.inArray(i, lb) + 1] =
                        vul.delay;
                }
            });
            $.each(dataSet, function (key, vul) {
                for (var i = 0; i <= lb.length; i++) {
                    if (vul[i] === undefined) {
                        vul[i] = 0;
                    }
                }
            });
        } else {
            $.each(v, function (key, vul) {
                lb =
                    vul.table_id != undefined
                        ? chartTable2.data.labels
                        : chartDish2.data.labels;
                var t = [];
                if ($.inArray(key, tableName) < 0) {
                    tableName.push(key);
                    t[0] = key;
                    t[$.inArray(i, lb) + 1] = vul.quantity_sum;
                    dataSet.push(t);
                } else {
                    dataSet[$.inArray(key, tableName)][$.inArray(i, lb) + 1] =
                        vul.quantity_sum;
                }
            });
            $.each(dataSet, function (key, vul) {
                for (var i = 0; i <= lb.length; i++) {
                    if (vul[i] === undefined) {
                        vul[i] = 0;
                    }
                }
            });
        }
    });
    columnsData = [{ title: "Nazwa" }];
    $.each(data, function (i, v) {
        var obj = {};
        obj.title = i;
        columnsData.push(obj);
    });

    if (dataSet.length > 0) {
        if ($.fn.dataTable.isDataTable("#" + select)) {
            if (!select.includes("2") && !select.includes("Delay")) {
                $("#" + select)
                    .dataTable()
                    .fnClearTable();
                $("#" + select)
                    .dataTable()
                    .fnAddData(dataSet);
            } else {
                $("#" + select)
                    .DataTable()
                    .destroy();
                $("#" + select).empty();
                tabl[select] = $("#" + select).DataTable({
                    data: dataSet,
                    columns: columnsData,
                    scrollX: true,
                    language: {
                        url: "/js/admin/Polish.json",
                    },
                });
            }
        } else {
            if (!select.includes("2") && !select.includes("Delay")) {
                tabl[select] = $("#" + select).DataTable({
                    data: dataSet,
                    columns: [
                        { title: "Nazwa" },
                        { title: paidLang },
                        { title: noPaidLang },
                    ],
                    language: {
                        url: "/js/admin/Polish.json",
                    },
                });
            } else {
                tabl[select] = $("#" + select).DataTable({
                    data: dataSet,
                    columns: columnsData,
                    language: {
                        url: "/js/admin/Polish.json",
                    },
                });
            }
        }
    } else {
        if (tabl[select] !== undefined) {
            $("#" + select)
                .dataTable()
                .fnClearTable();
        }
    }
}

var lineChartDataSets = [];
function setdataforTables2(data, name) {
    if (lineChartDataSets.length > 0) {
        lineChartDataSets = [];
    }
    var tbl = [];
    $.each(data, function (i, v) {
        var hue =
            "rgba(" +
            Math.floor(Math.random() * 256) +
            "," +
            Math.floor(Math.random() * 256) +
            "," +
            Math.floor(Math.random() * 256) +
            ", .5)";
        var obj = {};
        if ($.inArray(v[name], tbl) == -1) {
            tbl.push(v[name]);
            obj.label = v[name];
            obj.backgroundColor = hue;
            obj.borderColor = false;
            obj.borderWidth = 1;
            obj.data = [];
            lineChartDataSets.push(obj);
        }
    });
    if (name === "dish_name") {
        chartDish2.data.datasets = lineChartDataSets;
        chartDish2.update();
    } else {
        chartTable2.data.datasets = lineChartDataSets;
        chartTable2.update();
    }
}

var lineChartDataSets3 = [];
function setdataforTables3(data, name) {
    if (lineChartDataSets3.length > 0) {
        lineChartDataSets3 = [];
    }
    var tbl = [];
    $.each(data, function (i, v) {
        var hue =
            "rgba(" +
            Math.floor(Math.random() * 256) +
            "," +
            Math.floor(Math.random() * 256) +
            "," +
            Math.floor(Math.random() * 256) +
            ", .5)";
        var obj = {};
        if ($.inArray(v[name], tbl) == -1) {
            tbl.push(v[name]);
            obj.label = v[name];
            obj.backgroundColor = hue;
            obj.borderColor = false;
            obj.borderWidth = 1;
            obj.data = [];
            lineChartDataSets3.push(obj);
        }
    });
    if (name === "dish_name") {
        chartDish3.data.datasets = lineChartDataSets3;
        chartDish3.update();
    } else {
        chartTable3.data.datasets = lineChartDataSets3;
        chartTable3.update();
    }
}

var globalData = {
        table: {},
        dish: {},
    },
    pre = "",
    tabl = [];

var paidLang = $("html").attr("lang") == "pl" ? "opłacone" : "paid";
var noPaidLang = $("html").attr("lang") == "pl" ? "nie opłacone" : "no paid";

$(document).ready(function () {
    $("#idForm2").on("submit", function () {
        var g =
            $("[time]:focus").attr("name") !== undefined
                ? $("[time]:focus").attr("name")
                : "dayname";

        $.get(
            "/admin/dashboard/table-date",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
                group: g, //dayname, monthname, time, year
            },
            function (json, status) {
                clearData(chartTable2);
                if (
                    typeof json.results !== "undefined" &&
                    json.results.length > 0
                ) {
                    setdataforTables2(json.results, "table_name");
                    setDataFormResponse(json.results, "table2");
                    setChartData(
                        globalData.table2,
                        "quantity_sum",
                        chartTable2,
                    );
                    chartList(globalData.table2, "listTable2", "quantity_sum");
                }
            },
            "json",
        );

        return false;
    });
    $("#idForm3").on("submit", function () {
        var g =
            $("[time]:focus").attr("name") !== undefined
                ? $("[time]:focus").attr("name")
                : "dayname";
        $.get(
            "/admin/dashboard/dish-date",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
                group: g, //dayname, monthname, time, year
            },
            function (json, status) {
                clearData(chartDish2);
                if (
                    typeof json.results !== "undefined" &&
                    json.results.length > 0
                ) {
                    setdataforTables2(json.results, "dish_name");
                    setDataFormResponse(json.results, "dish2");
                    setChartData(globalData.dish2, "quantity_sum", chartDish2);
                    chartList(globalData.dish2, "listDish2", "quantity_sum");
                }
            },
            "json",
        );

        return false;
    });
    $("#idForm4").on("submit", function () {
        var g =
            $("[time]:focus").attr("name") !== undefined
                ? $("[time]:focus").attr("name")
                : "dayname";
        $.get(
            "/admin/dashboard/table-delay",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
                group: g, //dayname, monthname, time, year
            },
            function (json, status) {
                clearData(chartTable3);
                if (
                    typeof json.results !== "undefined" &&
                    json.results.length > 0
                ) {
                    setdataforTables3(json.results, "table_name");
                    setDataFormResponse(json.results, "table3");
                    setChartData(globalData.table3, "delay", chartTable3);
                    chartList(globalData.table3, "listDelayTable", "delay");
                }
            },
            "json",
        );

        return false;
    });
    $("#idForm5").on("submit", function () {
        var g =
            $("[time]:focus").attr("name") !== undefined
                ? $("[time]:focus").attr("name")
                : "dayname";
        $.get(
            "/admin/dashboard/dish-delay",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
                group: g, //dayname, monthname, time, year
            },
            function (json, status) {
                if (typeof chartDish3 !== "undefined") clearData(chartDish3);
                if (
                    typeof chartDish3 !== "undefined" &&
                    typeof json.results !== "undefined" &&
                    json.results.length > 0
                ) {
                    setdataforTables3(json.results, "dish_name");
                    setDataFormResponse(json.results, "dish3");
                    setChartData(globalData.dish3, "delay", chartDish3);
                    chartList(globalData.dish3, "listDelayDish", "delay");
                }
            },
            "json",
        );

        return false;
    });
    $("#idForm").on("submit", function () {
        $.get(
            "/admin/dashboard/table",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
            },
            function (json, status) {
                if (typeof chartTable !== "undefined") clearData(chartTable);
                if (
                    typeof chartTable !== "undefined" &&
                    typeof json.results !== "undefined" &&
                    json.results.length > 0
                ) {
                    setDataFormResponse(json.results, "table");
                    setChartData(globalData.table, "quantity_sum", chartTable);
                    chartList(globalData.table, "listTable", "quantity_sum");
                }
            },
            "json",
        );

        $.get(
            "/admin/dashboard/dish",
            {
                createdAt: {
                    start: $("#createAtStart").val(),
                    end: $("#createAtEnd").val(),
                },
            },
            function (json, status) {
                if (typeof chartDish !== "undefined") clearData(chartDish);
                if (
                    typeof chartDish !== "undefined" &&
                    typeof json.results !== "undefined" &&
                    json.results.length > 0
                ) {
                    setDataFormResponse(json.results, "dish");
                    setChartData(globalData.dish, "quantity_sum", chartDish);
                    chartList(globalData.dish, "listDish", "quantity_sum");
                }
            },
            "json",
        );

        tabl = [];
        dataSet = [];
        $.get(
            "/admin/dashboard/restaurant",
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
    $("[time]").on("click", function () {
        var father = $(this).closest("form");
        $(father).submit();
    });
    $("#createAtStart,#createAtEnd").datepicker({
        onHide: function () {
            $("#idForm").submit();
            $("#idForm2").submit();
            $("#idForm3").submit();
            $("#idForm4").submit();
            $("#idForm5").submit();
        },
    });
    $("[sort]").on("click", function () {
        clearData($(this).attr("chart") == "Table" ? chartTable : chartDish);
        setChartData(
            $(this).attr("chart") == "Table"
                ? globalData.table
                : globalData.dish,
            $(this).attr("sort"),
            $(this).attr("chart") == "Table" ? chartTable : chartDish,
        );
        $(this)
            .addClass("active")
            .parent()
            .find(".active")
            .not(this)
            .removeClass("active");
        pre = $(this).attr("sort") != "quantity_sum" ? "PLN" : "";
        window["chart" + $(this).attr("chart")].update();
        chartList(
            $(this).attr("chart") == "Table"
                ? globalData.table
                : globalData.dish,
            "list" + $(this).attr("chart"),
            $(this).attr("sort"),
        );
    });

    if ($("#chartTable").length)
        window.chartTable = new Chart($("#chartTable"), {
            type: "bar",
            data: {
                labels: [],
                datasets: [
                    {
                        label: paidLang,
                        backgroundColor: "rgba(255, 0, 0, .5)",
                        borderColor: "rgba(255, 0, 0, 1)",
                        borderWidth: 1,
                        data: [],
                    },
                    {
                        label: noPaidLang,
                        backgroundColor: "rgba(98, 0, 238, .5)",
                        borderColor: "#6200EE",
                        borderWidth: 1,
                        data: [],
                    },
                ],
            },
            options: {
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [
                        {
                            type: "linear",
                            ticks: {
                                callback: function (value) {
                                    return value + pre;
                                },
                            },
                        },
                    ],
                },
            },
        });
    if ($("#chartTable2").length)
        window.chartTable2 = new Chart($("#chartTable2"), {
            type: "line",
            data: {
                labels: [],
                datasets: null,
            },
            options: {
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [
                        {
                            type: "linear",
                            ticks: {
                                callback: function (value) {
                                    return value + pre;
                                },
                            },
                        },
                    ],
                },
                tooltips: {
                    callbacks: {
                        labelColor: function (tooltipItem, chart) {
                            keysDish = Object.keys(globalData.table2);
                            objPaid =
                                globalData.table2[keysDish[tooltipItem.index]];
                            var v =
                                objPaid[
                                    chart.data.datasets[
                                        tooltipItem.datasetIndex
                                    ].label
                                ];
                            tooltipItem.yLabel =
                                "quantity_sum: " +
                                v["quantity_sum"] +
                                ", price_sum: " +
                                v["price_sum"] +
                                ", price_avg:" +
                                v["price_avg"];
                            return {
                                borderColor: false,
                                backgroundColor:
                                    chart.data.datasets[
                                        tooltipItem.datasetIndex
                                    ].backgroundColor,
                            };
                        },
                    },
                },
            },
        });
    if ($("#chartDish").length)
        window.chartDish = new Chart($("#chartDish"), {
            type: "bar",
            data: {
                labels: [],
                datasets: [
                    {
                        label: paidLang,
                        backgroundColor: "rgba(255, 0, 0, .5)",
                        borderColor: "rgba(255, 0, 0, 1)",
                        borderWidth: 1,
                        data: [],
                    },
                    {
                        label: noPaidLang,
                        backgroundColor: "rgba(98, 0, 238, .5)",
                        borderColor: "#6200EE",
                        borderWidth: 1,
                        data: [],
                    },
                ],
            },
            options: {
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [
                        {
                            type: "linear",
                            ticks: {
                                callback: function (value) {
                                    return value + pre;
                                },
                            },
                        },
                    ],
                },
                tooltips: {
                    callbacks: {
                        labelColor: function (tooltipItem, chart) {
                            keysDish = Object.keys(globalData.dish);
                            objPaid =
                                globalData.dish[keysDish[tooltipItem.index]];
                            if (objPaid["paid"].hasOwnProperty("dish_name")) {
                                tooltipItem.xLabel =
                                    objPaid["paid"]["dish_name"];
                            } else {
                                tooltipItem.xLabel =
                                    objPaid["noPaid"]["dish_name"];
                            }
                            if (objPaid["paid"].hasOwnProperty("dish_name")) {
                                return {
                                    borderColor: "#6200EE",
                                    backgroundColor: "rgba(98, 0, 238, .5)",
                                };
                            } else {
                                return {
                                    borderColor: "rgba(255, 0, 0, 1)",
                                    backgroundColor: "rgba(255, 0, 0, .5)",
                                };
                            }
                        },
                    },
                },
            },
        });
    if ($("#chartDish2").length)
        window.chartDish2 = new Chart($("#chartDish2"), {
            type: "line",
            data: {
                labels: [],
                datasets: null,
            },
            options: {
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [
                        {
                            type: "linear",
                            ticks: {
                                callback: function (value) {
                                    return value + pre;
                                },
                            },
                        },
                    ],
                },
                tooltips: {
                    callbacks: {
                        labelColor: function (tooltipItem, chart) {
                            keysDish = Object.keys(globalData.dish2);
                            objPaid =
                                globalData.dish2[keysDish[tooltipItem.index]];
                            var dish =
                                objPaid[
                                    chart.data.datasets[
                                        tooltipItem.datasetIndex
                                    ].label
                                ];
                            tooltipItem.yLabel =
                                "quantity_sum: " +
                                dish["quantity_sum"] +
                                ", price_sum: " +
                                dish["price_sum"] +
                                ", price_avg:" +
                                dish["price_avg"];
                            return {
                                borderColor: false,
                                backgroundColor:
                                    chart.data.datasets[
                                        tooltipItem.datasetIndex
                                    ].backgroundColor,
                            };
                        },
                    },
                },
            },
        });

    if ($("#chartDelayDish").length)
        window.chartDish3 = new Chart($("#chartDelayDish"), {
            type: "bar",
            data: {
                labels: [],
                datasets: [],
            },
            options: {
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [
                        {
                            type: "linear",
                            ticks: {
                                min: 0,
                                callback: function (value) {
                                    return value + pre;
                                },
                            },
                        },
                    ],
                },
            },
        });

    if ($("#chartDelayTable").length)
        window.chartTable3 = new Chart($("#chartDelayTable"), {
            type: "bar",
            data: {
                labels: [],
                datasets: [],
            },
            options: {
                title: {
                    display: false,
                },
                scales: {
                    yAxes: [
                        {
                            type: "linear",
                            ticks: {
                                min: 0,
                                callback: function (value) {
                                    return value + pre;
                                },
                            },
                        },
                    ],
                },
            },
        });
    $("#idForm").submit();
    $("#idForm2").submit();
    $("#idForm3").submit();
    $("#idForm4").submit();
    $("#idForm5").submit();
});
