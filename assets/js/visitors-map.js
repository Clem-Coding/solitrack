document.addEventListener("DOMContentLoaded", function () {
  const isMobile = window.innerWidth <= 768;
  const mapCenter = isMobile ? [48, -2.3] : [46.5, 0.2];
  const mapZoom = isMobile ? 6.5 : 5.5;
  const map = L.map("map").setView(mapCenter, mapZoom);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
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
});
