<template>
    <input :value="coordinatesFormatForSave" name="range_polygon" type="hidden" />
    <div id="map" class="map-container"></div>
</template>

<script setup>
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import "leaflet-draw/dist/leaflet.draw.css";
import "leaflet-draw";
import drawLocales from "leaflet-draw-locales";
import { getHostname, locale, sendRequest } from "../../../helpers";
import icon from "../../assets/map-icon.png";

import { computed, onMounted, ref } from "vue";

const INITIAL_CONTROLS_STATE = [
    true, false, false, false, false, false
];

const NO_CONTROLS_STATE = [
    false, false, false, false, false, false
];

const props = defineProps({
    readonly: {
        type: Boolean,
        default: false
    },
    deliveryRanges: {
        type: [],
        required: false,
        default: null
    }
});
const map = ref(null);
const tiles = ref(null);
const drawControl = ref(null);
const editableLayers = ref(null);
const coordinates = ref(null);
const restaurantCoordinates = ref(null);
const restaurantAddress = ref(null);
const restaurantName = ref(null);
const markerIcon = ref(null);
const parsedCoordinates = ref(null);
const drawnPolygons = ref([]);
const pointsCount = ref(0);

const coordinatesFormatForSave = computed(() => {
    let formattedString = "";
    if (!!coordinates.value) {
        coordinates.value?.map((item, index) => {
            if (coordinates.value.length - 1 === index) {
                formattedString += `[${item[0]}, ${item[1]}]`;
            } else {
                formattedString += `[${item[0]}, ${item[1]}],`;
            }
        });
        formattedString = `[${formattedString}]`;
    }

    return formattedString;
});

const parseCoordinatesString = (coordinates) => {
    return coordinates.map((item) => {
        return JSON.parse(item);
    });
};

const fetchCoordinates = async () => {
    return await sendRequest(`${getHostname()}/admin/delivery_ranges/coordinates`);
};

const calculateColorForPolygon = (indexOfPolygon) => {
    const hsl = "hsl(354,87%,46%)";
    const hslParts = hsl.match(/(\d+),\s*(\d+)%,\s*(\d+)%/);

    let h = parseInt(hslParts[1]);
    let s = parseInt(hslParts[2]);
    let l = parseInt(hslParts[3]);

    l = Math.min(100, l + (0.08 * indexOfPolygon) * 100);

    return `hsl(${h}, ${s}%, ${l}%)`;
};

const drawPolygons = (coordinatesArray) => {
    drawnPolygons.value = [];
    coordinatesArray.forEach((item, index) => {
        if (!!item) {
            const polygon = L.polygon([item], { color: calculateColorForPolygon(index) }).addTo(map.value);
            drawnPolygons.value.push(polygon);
            map.value.addLayer(polygon);
        }
    });
};

const removePolygons = () => {
    drawnPolygons.value.forEach((item) => {
        map.value.removeLayer(item);
    });
};

const initDeliveryRangePolygons = async () => {
    const { coordinates, restaurantCords, address, restaurantName: name } = await fetchCoordinates();
    restaurantCoordinates.value = restaurantCords;
    restaurantAddress.value = address;
    restaurantName.value = name;

    parsedCoordinates.value = parseCoordinatesString(coordinates);
};

const updateCoordinates = (layer) => {
    coordinates.value = layer.getLatLngs()[0].map((item) => {
        return [item.lat, item.lng];
    });

    map.value.addLayer(layer);
    editableLayers.value.addLayer(layer);
};


const handleDrawEventCreated = (event) => {
    const layer = event.layer;
    updateCoordinates(layer);
    map.value.removeControl(drawControl.value);
    setUpControls(...NO_CONTROLS_STATE);
};

const handleDrawEventEdited = (event) => {
    const layers = event.layers;
    layers.eachLayer((layer) => {
        updateCoordinates(layer);
    });
};

const handleDrawEventDeleted = (event) => {
    const layers = event.layers;
    let isDeleted = false;

    layers.eachLayer(function(layer) {
        if (layer.getLatLngs()[0].length > 0) {
            isDeleted = true;
        }
    });

    if (isDeleted) {
        coordinates.value = null;
        pointsCount.value = 0;
        map.value.removeControl(drawControl.value);
        setUpControls(...INITIAL_CONTROLS_STATE);
    }
};

const handleDrawEventDrawStarted = (event) => {
    pointsCount.value = 0;
    actionButtonsUpdate();
};

const handleDrawEventDrawVertex = (event) => {
    let localPointsCount = 0;
    event.layers.eachLayer((layer) => {
        localPointsCount++;
    });

    pointsCount.value = localPointsCount;
    actionButtonsUpdate();
};


const actionButtonsUpdate = () => {
    const menu = document.querySelector(".leaflet-draw-actions");
    const buttons = menu?.querySelectorAll("li");
    if (buttons) {
        buttons[0].style.display = pointsCount.value < 3 ? "none" : "";
        buttons[1].style.display = pointsCount.value < 2 ? "none" : "";
    }
};

const setUpControls = (polygon, polyline, rectangle, circle, marker, circlemarker) => {
    drawControl.value = new L.Control.Draw({
        edit: {
            featureGroup: editableLayers.value
        },
        draw: {
            polygon,
            polyline,
            rectangle,
            circle,
            marker,
            circlemarker
        }
    });

    L.drawLocal = drawLocales(locale());

    map.value.addControl(drawControl.value);
};

const configureDrawing = () => {
    editableLayers.value = new L.FeatureGroup();
    map.value.addLayer(editableLayers.value);

    setUpControls(...INITIAL_CONTROLS_STATE);

    map.value.on(L.Draw.Event.CREATED, (event) => handleDrawEventCreated(event));
    map.value.on(L.Draw.Event.EDITED, (event) => handleDrawEventEdited(event));
    map.value.on(L.Draw.Event.DELETED, (event) => handleDrawEventDeleted(event));
    map.value.on(L.Draw.Event.DRAWSTART, (event) => handleDrawEventDrawStarted(event));
    map.value.on(L.Draw.Event.DRAWVERTEX, (event) => handleDrawEventDrawVertex(event));
};
const fitMapZoom = () => {
    let largestArea = 0;
    let largestPolygon = null;
    drawnPolygons.value.forEach((item) => {
        const area = L.GeometryUtil.geodesicArea(item.getLatLngs()[0]);
        if (area > largestArea) {
            largestArea = area;
            largestPolygon = item;
        }
    });

    if (!!largestPolygon) {
        map.value.fitBounds(largestPolygon.getBounds());
    }
};

const configureMarkerIcon = () => {
    markerIcon.value = L.icon({
        iconUrl: icon,
        iconSize: [50, 35]
    });

};

onMounted(async () => {
    await initDeliveryRangePolygons();
    configureMarkerIcon();
    map.value = L.map("map").setView([parseFloat(restaurantCoordinates.value.lat), parseFloat(restaurantCoordinates.value.lng)], 999);
    tiles.value = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"
    }).addTo(map.value);

    if (!props.readonly) {
        configureDrawing();
    }

    L.marker([parseFloat(restaurantCoordinates.value.lat), parseFloat(restaurantCoordinates.value.lng)], { icon: markerIcon.value }).addTo(map.value).bindPopup(`${restaurantName.value} - ${restaurantAddress.value}`);
    drawPolygons(parsedCoordinates.value);
    fitMapZoom();

    window.addEventListener("deleteDeliveryRange", async () => {
        await initDeliveryRangePolygons();
        removePolygons();
        drawPolygons(parsedCoordinates.value);
        fitMapZoom();
    });

});

</script>

<style scoped>
.map-container {
    width: 100%;
    z-index: 0;
    height: 450px;
}

</style>
