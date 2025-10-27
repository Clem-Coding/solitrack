import { getFrenchMonthName } from "./helpers/utils.js";
import { EMERALD_SEA, CORAL } from "./helpers/constants.js";

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
      sendToAPI(filterPeriod.value, filterType.value, selectedYear);
    });
  }

  function handleMonthChange() {
    monthPicker.addEventListener("change", () => {
      const selectedMonth = monthSelect.value;
      sendToAPI(filterPeriod.value, filterType.value, null, selectedMonth);
    });
  }

  function sendToAPI() {
    const period = filterPeriod.value;
    const type = filterType.value;
    const year = yearInput.value;
    const month = monthSelect.value;
    let category = getCategoryFromPath();
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

    // Category is the last part of the path
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
    // Set canvas size to parent size
    const canvas = document.getElementById("acquisitions");
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;

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

    const category = getCategoryFromPath();
    let yearFromMonthlySlection = yearInput.value;

    const period = filterPeriod.value;
    const type = filterType.value;

    const [year, month] = monthSelect.value.split("-");
    const days = getDaysInMonth(year, month);

    // Data Formatting

    let formattedData;

    if (type === "both") {
      formattedData = {
        incoming: data.incoming.map(formatItem),
        outgoing: data.outgoing.map(formatItem),
      };
    } else {
      formattedData = data.map(formatItem);
    }

    function formatItem(item) {
      if (category === "visiteurs" && period === "yearly") {
        return Number(item.totalData);
      } else if (category === "visiteurs") {
        return Number(item);
      } else if (period === "yearly") {
        return Number(item.totalData).toFixed(2);
      } else {
        return Number(item).toFixed(2);
      }
    }

    // Config Datasets
    let datasets;

    if (type === "both") {
      datasets = [
        {
          label: "Total des poids entrants",
          data: formattedData.incoming,
          backgroundColor: CORAL,
        },
        {
          label: "Total des poids sortants",
          data: formattedData.outgoing,
          backgroundColor: EMERALD_SEA,
        },
      ];
    } else {
      datasets = [
        {
          label: "Total des poids",
          data: formattedData,
          backgroundColor: CORAL,
        },
      ];
    }

    //Config Labels
    let labels;

    if (period === "yearly") {
      labels = (type === "both" ? data.incoming : data).map((item) => item.year);
    } else if (period === "monthly") {
      labels = months;
    } else {
      labels = days;
    }

    //remove the previous chart instance if it exists because Chart.js does not support re-rendering the same canvas element
    if (chartInstance) {
      chartInstance.destroy();
    }

    // Create the chart instance
    chartInstance = new Chart(document.getElementById("acquisitions"), {
      type: "bar",
      data: {
        labels: labels,
        datasets: datasets,
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              title: function (context) {
                const label = context[0].label;
                if (period === "monthly") {
                  return `${label} ${yearFromMonthlySlection}`;
                }
                if (period === "yearly") {
                  return `Année ${label}`;
                }
                if (period === "daily") {
                  return `${label} ${getFrenchMonthName(month)} ${year}`;
                }
              },
              label: function (context) {
                const rawValue = context.raw;
                if (category === "ventes") {
                  return `${rawValue} ${"€"}`;
                } else if (category === "visiteurs") {
                  return `${rawValue} ${" visiteurs"}`;
                } else {
                  return `${rawValue} ${"kg"}`;
                }
              },
            },
          },
          legend: {
            display: category === "vetements" || category === "articles",
          },
        },
      },
    });
  }

  // ==========================
  // 🚀 INITIALIZATION
  // ==========================

  // setActiveSidebarButton(event);
  function initializePage() {
    filterType.selectedIndex = 0;
    filterPeriod.selectedIndex = 0;
    yearInput.selectedIndex = 0;

    handlePeriodFilterChange();
    handleTypeFilterChange();
    sendToAPI();
    handleYearChange();
    handleMonthChange();
  }

  initializePage();
});
