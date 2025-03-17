document.addEventListener("DOMContentLoaded", async function () {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const filterPeriod = document.getElementById("filter-period");
  const filterType = document.getElementById("filter-type");

  const datePicker = document.getElementById("date-picker");
  const yearPicker = document.getElementById("month-picker");

  const apiUrl = "/api/statistiques/";
  let chartInstance = null;

  // ==========================
  // 🟢 FETCH API (Fetching data)
  // ==========================
  async function fetchData(category, type, period) {
    console.log(`Fetching data with period: ${period}, category: ${category}, and type: ${type}`);
    try {
      const response = await fetch(`${apiUrl}?period=${period}&category=${category}&type=${type}`);
      console.log(response);
      const data = await response.json();
      console.log("Data fetched:", data.data);

      // Appel de la fonction pour créer ou mettre à jour le graphique
      createGraph(data.data);
    } catch (error) {
      console.error("Error while fetching data:", error);
    }
  }

  // ==========================
  // 🔧 HANDLE FILTER CHANGES
  // ==========================
  function handlePeriodFilterChange() {
    filterPeriod.addEventListener("change", () => {
      const selectedPeriod = filterPeriod.value;
      console.log("Selected period:", selectedPeriod);
      sendToAPI(selectedPeriod);
    });
  }

  function handleTypeFilterChange() {
    filterType.addEventListener("change", () => {
      const selectedType = filterType.value;
      console.log("Selected type:", selectedType);
      sendToAPI(null, selectedType); // Pass null for period to keep the current value
    });
  }

  // Envoie les données à l'API avec les filtres sélectionnés
  function sendToAPI(period = filterPeriod.value, type = filterType.value) {
    let category = getCategoryFromPath();
    console.log(`Sending to API with category: ${category}, type: ${type}, period: ${period}`);
    fetchData(category, type, period);
  }

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================
  function togglePeriodView(period) {
    datePicker.style.display = period === "daily" ? "block" : "none";
    yearPicker.style.display = period === "monthly" ? "block" : "none";
  }

  function getCategoryFromPath() {
    const path = window.location.pathname;
    const parts = path.split("/");
    // Supposons que la catégorie est toujours le dernier segment de l'URL
    return parts[parts.length - 1];
  }

  // ==========================
  // 📊 FUNCTION TO CREATE GRAPH
  // ==========================
  function createGraph(data) {
    const months = [
      "Janvier",
      "Février",
      "Mars",
      "Avril",
      "Mai",
      "Juin",
      "Juillet",
      "Août",
      "Septembre",
      "Octobre",
      "Novembre",
      "Décembre",
    ];

    console.log("les datas!!", data);
    // const donationsFormatted = data.donations.map((donation) => donation.toFixed(2));

    // Si une instance de graphique existe déjà, on la détruit pour la mettre à jour
    if (chartInstance) {
      chartInstance.destroy();
    }

    // Créer un nouveau graphique
    chartInstance = new Chart(document.getElementById("acquisitions"), {
      type: "bar",
      data: {
        labels: months,
        datasets: [
          {
            label: "Total des poids entrants sur un an",
            data: data,
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
    handleTypeFilterChange();
    sendToAPI();
  }

  initializePage();
});
