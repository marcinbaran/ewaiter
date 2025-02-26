import { TimepickerUI } from "timepicker-ui";

// const calculateTime = (min, max) => {
//     const minTime = min.split(":").map((time) => parseInt(time));
//     const maxTime = max.split(":").map((time) => parseInt(time));
//     if (!minTime[1]) {
//         minTime[1] = 0;
//     }
//     if (!maxTime[1]) {
//         maxTime[1] = 0;
//     }
//     console.log(minTime, maxTime);
//     const timeString = `${maxTime[0]}:${maxTime[1]}-${minTime[0]}:${minTime[1]}`;
//     return timeString;
// };

const timePickerElements = document.querySelectorAll(".timepicker-ui");
timePickerElements.forEach((indicator) => {
    const myTimePicker = new TimepickerUI(indicator, {
        delayHandler: 0,
        clockType: "24h",
    });
    myTimePicker.create();
});
