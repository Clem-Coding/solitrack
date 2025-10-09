import L from "leaflet";
import "leaflet.fullscreen";
import { POPUP_COLOR_PRIMARY } from "./helpers/constants.js";

document.addEventListener("DOMContentLoaded", function () {
  const isMobile = window.innerWidth <= 768;
  const mapCenter = isMobile ? [48, -2.3] : [47, -2];
  const mapZoom = isMobile ? 6.5 : 5.5;
  const map = L.map("map").setView(mapCenter, mapZoom);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(map);

  const points = JSON.parse(document.getElementById("points-data").textContent);

  points.forEach(function (point) {
    const popupContent = `${point.city}, ${point.zipcode}, ${point.visitorCount} visiteur${
      point.visitorCount > 1 ? "s" : ""
    }`;
    L.marker([point.lat, point.lon]).addTo(map).bindPopup(popupContent);
  });

  window.addEventListener("resize", function () {
    const mapCenter = isMobile ? [48.1, -2.7] : [46.5, 1];
    map.setView(mapCenter, mapZoom);
  });

  // Pin "You are here"
  const plouasneLatLon = [48.304521, -2.010791];

  const pinIcon = L.divIcon({
    html: `<span style="
      font-size: 1.8rem;
    " aria-label="Vous êtes ici">📍</span>`,
    className: "your-location-pin",
    iconSize: [40, 40],
    iconAnchor: [24, 40],
    popupAnchor: [0, -40],
  });

  const topLayer = L.layerGroup().addTo(map);
  const youAreHereMarker = L.marker(plouasneLatLon, { icon: pinIcon, zIndexOffset: 1000 })
    .addTo(topLayer)
    .bindPopup(`<span style="font-size:1rem;color: ${POPUP_COLOR_PRIMARY};font-weight:500;">Vous êtes ici</span>`);

  youAreHereMarker.openPopup();

  // Fullscreen control
  L.control
    .fullscreen({
      position: "topleft",
      title: "Activer le mode plein écran",
      titleCancel: "Quitter le mode plein écran",
      forcePseudoFullscreen: false,
      fullscreenElement: map.getContainer(),
    })
    .addTo(map);
});
