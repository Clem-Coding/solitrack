document.addEventListener("DOMContentLoaded", function () {
  const inWeight = parseFloat(document.getElementById("inWeight").dataset.value);
  const outWeight = parseFloat(document.getElementById("outWeight").dataset.value);
  const ctx = document.getElementById("myPieChart").getContext("2d");

  const myPieChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: ["Entrées", "Sorties"],
      datasets: [
        {
          label: "Poids",
          data: [inWeight, outWeight],
          backgroundColor: ["#EB5A47", " #080222"],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const rawValue = context.raw;
              return `${rawValue} kg`;
            },
          },
        },
      },
    },
  });
});
