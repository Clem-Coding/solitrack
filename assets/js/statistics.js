document.addEventListener("DOMContentLoaded", async function () {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const filterPeriod = document.getElementById("filter-period");
  const filterType = document.getElementById("filter-type");

  const monthPicker = document.querySelector("#month-picker");
  const yearPicker = document.querySelector("#year-picker");
  const monthSelect = document.querySelector("#month");
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
      console.log("data", data.data);
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

  function handleMonthChange() {
    monthPicker.addEventListener("change", () => {
      const selectedMonth = monthSelect.value;
      console.log("Selected month:", selectedMonth);
      sendToAPI(filterPeriod.value, filterType.value, null, selectedMonth);
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

    if (period === "daily") {
      monthPicker.style.display = "block";
      yearPicker.style.display = "none";
    } else if (period === "monthly") {
      monthPicker.style.display = "none";
      yearPicker.style.display = "block";
    } else {
      monthPicker.style.display = "none";
      yearPicker.style.display = "none";
    }
  }

  function getCategoryFromPath() {
    const path = window.location.pathname;
    const parts = path.split("/");
    // Supposons que la catégorie est toujours le dernier segment de l'URL
    return parts[parts.length - 1];
  }

  function getDaysInMonth(year, month) {
    const daysInMonth = new Date(year, month, 0).getDate();
    return Array.from({ length: daysInMonth }, (_, i) => i + 1);
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
    const period = filterPeriod.value;

    const [year, month] = monthSelect.value.split("-");
    const days = getDaysInMonth(year, month);

    console.log("les datas!!", data);
    const formattedData = data.map((data) => Number(data).toFixed(2));

    if (chartInstance) {
      chartInstance.destroy();
    }

    // Créer un nouveau graphique
    chartInstance = new Chart(document.getElementById("acquisitions"), {
      type: "bar",
      data: {
        labels: period === "monthly" ? months : days,
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
    handleMonthChange();
  }

  initializePage();
});
