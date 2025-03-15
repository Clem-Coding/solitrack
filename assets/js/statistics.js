// stats.js

document.addEventListener("DOMContentLoaded", function () {
  (async function () {
    const data = [
      { month: "Janvier", count: 402.3 },
      { month: "Février", count: 378.1 },
      { month: "Mars", count: 403.9 },
      { month: "Avril", count: 345.76 },
      { month: "Mai", count: 465.32 },
      { month: "Juin", count: 332.67 },
      { month: "Juillet", count: 387.98 },
      { month: "Août", count: 356.12 },
      { month: "Septembre", count: 476.34 },
      { month: "Octobre", count: 367.98 },
      { month: "Novembre", count: 464.23 },
      { month: "Décembre", count: 476.32 },
    ];

    new Chart(document.getElementById("acquisitions"), {
      type: "bar",
      data: {
        labels: data.map((row) => row.month),
        datasets: [
          {
            label: "Total des poids entrants sur un an",
            data: data.map((row) => row.count),
            backgroundColor: "#EB5A47",
          },
        ],
      },
      // options pour congigurer les tooltips du diagramme
      options: {
        plugins: {
          tooltip: {
            callbacks: {
              // fonction callback pour configurer le titre du Tooltip
              title: function (context) {
                const month = context[0].label; // Récupération du label pour le titre
                const year = new Date().getFullYear(); // Récupération de l'année en cours pour l'exemple

                return `${month} ${year}`;
              },
              // fonction callback pour configurer le label du Tooltip
              label: function (context) {
                const weightInKg = context.raw; //Récupération de la valeur brute du label

                return `${weightInKg} kg`;
              },
            },
          },
        },
      },
    });
  })();
});
