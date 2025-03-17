document.addEventListener("DOMContentLoaded", async function () {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const filterPeriod = document.getElementById("filter-period");
  const filterType = document.getElementById("filter-type");

  const datePicker = document.querySelector("#date-picker");
  const yearPicker = document.querySelector("#year-picker");
  const dateInput = document.querySelector("#date");
  const yearInput = document.querySelector("#year");

  const apiUrl = "/api/statistiques/";
  let chartInstance = null;

  // ==========================
  // 🟢 FETCH API
  // ==========================
  // ==========================
  async function fetchData(category, type, period, year = null, date = null) {
    console.log(
      `Fetching data with period: ${period}, category: ${category}, and type: ${type}, year: ${year}, date: ${date}`
    );

    try {
      let url = `${apiUrl}?period=${period}&category=${category}&type=${type}`;

      if (year) {
        url += `&year=${year}`;
      }

      if (date) {
        url += `&date=${date}`;
      }

      const response = await fetch(url);
      const data = await response.json();

      // Appel de la fonction pour créer ou mettre à jour le graphique
      createGraph(data.data);
      console.log("data", data);
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
      togglePeriodView(selectedPeriod);
      sendToAPI(selectedPeriod);
    });
  }

  function handleTypeFilterChange() {
    filterType.addEventListener("change", () => {
      const selectedType = filterType.value;
      sendToAPI(null, selectedType); // Pass null for period to keep the current value
    });
  }

  function handleYearChange() {
    yearPicker.addEventListener("change", () => {
      const selectedYear = yearInput.value;
      console.log("Selected year:", selectedYear);
      sendToAPI(filterPeriod.value, filterType.value, selectedYear);
    });
  }

  function handleDateChange() {
    datePicker.addEventListener("change", () => {
      const selectedDate = dateInput.value;
      console.log("Selected date:", selectedDate);
      sendToAPI(filterPeriod.value, filterType.value, null, selectedDate);
    });
  }

  function sendToAPI(period = filterPeriod.value, type = filterType.value, year = null, date = null) {
    let category = getCategoryFromPath();
    console.log(
      `Sending to API with category: ${category}, type: ${type}, period: ${period}, year: ${year}, date: ${date}`
    );
    fetchData(category, type, period, year, date);
  }

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================
  function togglePeriodView(period) {
    console.log("coucou le toggle");
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

    const days = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];

    console.log("les datas!!", data);
    const formattedData = data.map((data) => data.toFixed(2));

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
            data: formattedData,
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
    handleYearChange();
    handleDateChange();
  }

  initializePage();
});
