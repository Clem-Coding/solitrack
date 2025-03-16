document.addEventListener("DOMContentLoaded", async function () {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const filterPeriod = document.getElementById("filter-period");
  const datePicker = document.getElementById("date-picker");
  const yearPicker = document.getElementById("month-picker");

  const apiUrl = "/api/statistiques/";
  let chartInstance = null; // Instance du graphique, utile pour le mettre à jour

  // ==========================
  // 🟢 FETCH API (Fetching data)
  // ==========================
  async function fetchData(period, category) {
    try {
      const response = await fetch(`${apiUrl}?period=${period}&category=${category}`);
      console.log(response);
      const data = await response.json();
      console.log("Data fetched:", data);

      // Appel de la fonction pour créer ou mettre à jour le graphique
      // createGraph(data);
    } catch (error) {
      console.error("Error while fetching data:", error);
    }
  }

  // ==========================
  // 🔧 HANDLE FILTER CHANGES
  // ==========================
  function handlePeriodFilterChange() {
    console.log("coucou la fonction");
    filterPeriod.addEventListener("change", () => {
      const selectedPeriod = filterPeriod.value;
      // const selectedCategory = getSelectedCategory();

      togglePeriodView(selectedPeriod);
      fetchData(selectedPeriod);
    });
  }

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================

  function togglePeriodView(period) {
    datePicker.style.display = period === "daily" ? "block" : "none";
    yearPicker.style.display = period === "monthly" ? "block" : "none";
  }

  // ==========================
  // 📊 FUNCTION TO CREATE GRAPH
  // ==========================
  function createGraph(data) {
    const donationsFormatted = data.donations.map((donation) => donation.toFixed(2));

    // Si une instance de graphique existe déjà, on la détruit pour la mettre à jour
    if (chartInstance) {
      chartInstance.destroy();
    }

    // Créer un nouveau graphique
    chartInstance = new Chart(document.getElementById("acquisitions"), {
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

  // ==========================
  // 🚀 INITIALIZATION
  // ==========================
  function initializePage() {
    handlePeriodFilterChange();
  }

  initializePage();
});
