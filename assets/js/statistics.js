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

  async function fetchData(category, type, period, year = null, month = null) {
    console.log(
      `Fetching data with period: ${period}, category: ${category}, and type: ${type}, year: ${year}, month: ${month}`
    );

    try {
      let url = `${apiUrl}?period=${period}&category=${category}&type=${type}`;

      if (year) {
        url += `&year=${year}`;
      }

      if (month) {
        url += `&month=${month}`;
      }

      const response = await fetch(url);
      const data = await response.json();

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

  function handleMonthChange() {
    monthPicker.addEventListener("change", () => {
      const selectedMonth = monthSelect.value;
      console.log("Selected month:", selectedMonth);
      sendToAPI(filterPeriod.value, filterType.value, null, selectedMonth);
    });
  }

  function sendToAPI() {
    const period = filterPeriod.value;
    const type = filterType.value;
    const year = yearInput.value;
    const month = monthSelect.value;
    let category = getCategoryFromPath();
    console.log(
      `Sending to API with category: ${category}, type: ${type}, period: ${period}, year: ${year}, month: ${month}`
    );
    fetchData(category, type, period, year, month);
  }

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================
  function togglePeriodView(period) {
    if (period === "daily") {
      monthPicker.classList.remove("hidden");
      yearPicker.classList.add("hidden");
    } else if (period === "monthly") {
      yearPicker.classList.remove("hidden");
      monthPicker.classList.add("hidden");
    } else {
      monthPicker.classList.add("hidden");
      yearPicker.classList.add("hidden");
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

    const formattedData = data.map((item) => {
      if (period === "yearly") {
        return Number(item.totalData).toFixed(2);
      } else {
        return Number(item).toFixed(2);
      }
    });

    console.log("LES DONNEES FORMATEES!!!!", formattedData);

    if (chartInstance) {
      chartInstance.destroy();
    }

    chartInstance = new Chart(document.getElementById("acquisitions"), {
      type: "bar",
      data: {
        labels: period === "yearly" ? data.map((item) => item.year) : period === "monthly" ? months : days,
        datasets: [
          {
            label: "Total des poids entrants",
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
                const label = context[0].label;
                return period === "yearly"
                  ? `Année ${label}`
                  : period === "monthly"
                  ? `${label} ${year}`
                  : `Jour ${label}`;
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
    // const selectedPeriod = filterPeriod.value;
    // console.log("selected period", selectedPeriod);
    // togglePeriodView(selectedPeriod);
    handlePeriodFilterChange();
    handleTypeFilterChange();
    sendToAPI();
    handleYearChange();
    handleMonthChange();
  }

  initializePage();
});
