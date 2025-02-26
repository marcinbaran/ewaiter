import Chart from "chart.js/auto";

const colors = {
    light: {
        primary: "#737700",
        text: "#111827",
        chartBorder: "#d1d5db",
        gridBorder: "#d1d5db",
    },
    dark: {
        primary: "#99b51e",
        text: "#f3f4f6",
        chartBorder: "#4b5563",
        gridBorder: "#4b5563",
    },
};

let charts = [];

const createNormalCharts = (isDarkTheme) => {
    const chartElements = document.querySelectorAll(".chart--normal");

    chartElements.forEach((chart) => {
        createNormalChart(isDarkTheme, chart);
    });
};

const createNormalChart = (isDarkTheme, chart, destroy = false) => {
    const canvas = chart.querySelector(".chart--canvas");
    if (destroy) {
        var chartInstance = charts.find(function (instance) {
            return instance.canvas === canvas;
        });

        if (chartInstance) {
            chartInstance.destroy();
        }
    }

    const attributes = {
        type: canvas.dataset.chartType,
        data: canvas.dataset.chartData,
        labels: canvas.dataset.chartLabels,
        datasetLegend: canvas.dataset.chartDatasetLegend,
    };

    const newLabelsArr = attributes.labels ? attributes.labels.split(";") : [];
    const newLabelsObj = {
        x: newLabelsArr[0] ? newLabelsArr[0] : "",
        y: newLabelsArr[1] ? newLabelsArr[1] : "",
    };

    const options = {
        type: attributes.type,
        data: JSON.parse(attributes.data) ?? [],
        options: {
            datasets: {
                pie: {
                    borderColor: isDarkTheme
                        ? colors.dark.chartBorder
                        : colors.light.chartBorder,
                    borderWidth: 1,
                },
            },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: attributes.datasetLegend === "true",
                },
            },
            scales: {
                x: {
                    title: {
                        display: newLabelsObj.x ? true : false,
                        text: newLabelsObj.x,
                        font: {
                            weight: "bold",
                        },
                    },
                },
                y: {
                    title: {
                        display: newLabelsObj.y ? true : false,
                        text: newLabelsObj.y,
                        font: {
                            weight: "bold",
                        },
                    },
                },
            },
        },
    };

    charts.push(new Chart(canvas, options));
};

const createMonsterCharts = (isDarkTheme) => {
    const chartElements = document.querySelectorAll(".chart--monster");

    chartElements.forEach((chart) => {
        const canvas = chart.querySelector(".chart--canvas");

        const attributes = {
            type: canvas.dataset.chartType,
            data: canvas.dataset.chartData,
            labels: canvas.dataset.chartLabels,
            datasetLegend: canvas.dataset.chartDatasetLegend,
        };

        const newLabelsArr = attributes.labels
            ? attributes.labels.split(";")
            : [];
        const newLabelsObj = {
            x: newLabelsArr[0] ? newLabelsArr[0] : "",
            y: newLabelsArr[1] ? newLabelsArr[1] : "",
        };

        const options = {
            type: attributes.type,
            data: JSON.parse(attributes.data),
            options: {
                datasets: {
                    pie: {
                        borderColor: isDarkTheme
                            ? colors.dark.chartBorder
                            : colors.light.chartBorder,
                        borderWidth: 1,
                    },
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: attributes.datasetLegend === "true",
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: newLabelsObj.x ? true : false,
                            text: newLabelsObj.x,
                            font: {
                                weight: "bold",
                            },
                        },
                    },
                    y: {
                        title: {
                            display: newLabelsObj.y ? true : false,
                            text: newLabelsObj.y,
                            font: {
                                weight: "bold",
                            },
                        },
                    },
                },
            },
        };
        const myChart = new Chart(canvas, options);

        myChart.canvas.onclick = (evt) => {
            const point = myChart.getElementsAtEventForMode(
                evt,
                "nearest",
                { intersect: true },
                true,
            )[0];
            const dataset = myChart.data.datasets[point.datasetIndex];
            const label = myChart.data.labels[point.index];
            const value = dataset.data[point.index];
        };

        charts.push(myChart);
    });
};

const createCharts = () => {
    const isDarkTheme = document
        .querySelector("html")
        .classList.contains("dark");
    Chart.defaults.color = isDarkTheme ? colors.dark.text : colors.light.text;
    Chart.defaults.borderColor = isDarkTheme
        ? colors.dark.gridBorder
        : colors.light.gridBorder;

    createNormalCharts(isDarkTheme);
    createMonsterCharts(isDarkTheme);
};

const destroyCharts = () => {
    charts.forEach((chart) => {
        chart.destroy();
    });
};

const checkIfTargetIsThemeButton = (target) => {
    if (target && target.id === "theme-toggle") {
        return true;
    } else if (target.parentElement) {
        return checkIfTargetIsThemeButton(target.parentElement);
    }
    return false;
};

document.addEventListener("click", (e) => {
    if (checkIfTargetIsThemeButton(e.target)) {
        destroyCharts();
        createCharts();
    }
});

window.addEventListener("chart-data-updated", (event) => {
    let chart = document.querySelector(`.${event.detail.container_class}`);
    const isDarkTheme = document
        .querySelector("html")
        .classList.contains("dark");
    setTimeout(() => {
        createNormalChart(isDarkTheme, chart, true);
    }, 400);
});

destroyCharts();
createCharts();
