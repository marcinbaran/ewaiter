import L from "leaflet";
import "leaflet/dist/leaflet.css";

const mapIndicators = document.querySelectorAll(".map-initialize");
var coords = [0, 0];
var setCoordsByAddress = function (address, map) {
    var url =
        "https://nominatim.openstreetmap.org/search?q=" +
        encodeURIComponent(address) +
        "&format=json";

    var latitude = 0;
    var longitude = 0;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.length > 0) {
                addMarkerToMap(response[0].lat, response[0].lon, address, map);
            } else {
                console.log("Nie znaleziono współrzędnych dla podanego adresu");
            }
        } else {
            console.log(
                "Wystąpił problem podczas zapytania do OpenStreetMap Nominatim API",
            );
        }
    };
    xhr.send();
};

var addMarkerToMap = function (latitude, longitude, label, map) {
    var customIcon = L.icon({
        iconUrl: "/images/map/marker-icon.png",
        iconSize: [25, 32], // rozmiar ikony w pikselach
        iconAnchor: [13, 16], // punkt, na którym ikona będzie "przyklejona" do współrzędnych markera
    });

    var marker = L.marker([latitude, longitude], { icon: customIcon }).addTo(
        map,
    );
    marker.bindPopup(label).openPopup();
    map.setView([latitude, longitude], 16);
};

mapIndicators.forEach((indicator) => {
    var address = indicator.getAttribute("data-address");
    var label = indicator.getAttribute("data-label");
    var id = indicator.getAttribute("id");
    var map = L.map(id).setView([0, 0], 13);
    setCoordsByAddress(address, map);

    const theme = localStorage.getItem("color-theme");
    chooseTheme(theme, map);
});

export function chooseTheme(theme, map) {
    if (theme == "dark") {
        L.tileLayer(
            "https://maps.geoapify.com/v1/tile/dark-matter-yellow-roads/{z}/{x}/{y}@2x.png?apiKey=957df63968974f8d9fa599960a3a6c58",
            {
                attribution:
                    'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                maxZoom: 18,
                apiKey: "957df63968974f8d9fa599960a3a6c58",
                id: "dark-matter-yellow-roads",
            },
        ).addTo(map);
    } else {
        L.tileLayer(
            "https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}@2x.png?apiKey=957df63968974f8d9fa599960a3a6c58",
            {
                attribution:
                    'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                maxZoom: 18,
                apiKey: "957df63968974f8d9fa599960a3a6c58",
                id: "osm-bright",
            },
        ).addTo(map);
    }
}
