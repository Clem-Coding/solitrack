// stats.js

document.addEventListener("DOMContentLoaded", async function () {
  try {
    const response = await fetch("api/statistiques/", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    // Vérification de la réponse
    if (!response.ok) {
      throw new Error(`Erreur réseau: ${response.statusText}`);
    }

    // Conversion de la réponse en JSON
    const data = await response.json();

    // Affichage des données dans la console
    console.log("Données JSON:", data);
    console.log("Donations:", data.donations);
    console.log("Mois:", data.months);

    createGraph(data);
  } catch (error) {
    console.error("Erreur lors de la récupération des données:", error);
  }
});

// Fonction pour créer un graphique
function createGraph(data) {
  const donationsFormatted = data.donations.map((donation) => donation.toFixed(2));

  new Chart(document.getElementById("acquisitions"), {
    type: "bar",
    data: {
      labels: data.months,
      datasets: [
        {
          label: "Total des poids entrants sur un an",
          data: donationsFormatted,
          backgroundColor: "#EB5A47",
          borderColor: "#080222",
          borderWidth: 2,
        },
      ],
    },
    options: {
      plugins: {
        tooltip: {
          callbacks: {
            title: function (context) {
              const month = context[0].label;
              const year = new Date().getFullYear();
              return `${month} ${year}`;
            },

            label: function (context) {
              const weightInKg = context.raw;
              return `${weightInKg} kg`;
            },
          },
        },
      },
    },
  });
}
