import { formatNumber, formatInputValue } from "./helpers/utils.js";

document.addEventListener("DOMContentLoaded", () => {
  const countedBalanceEl = document.querySelector("#cash_register_closure_countedBalance");
  const discrepancyEl = document.querySelector("#cash_register_closure_discrepancy");
  const theoreticalBalanceEl = document.querySelector("#theoreticalBalance");
  const theoreticalBalance = theoreticalBalanceEl ? Number(theoreticalBalanceEl.textContent.replace(",", ".")) : 0;
  const noteButton = document.querySelector(".note-toggle");
  const noteTextarea = document.querySelector(".form-group textarea#cash_register_closure_note");
  const noteGroup = noteTextarea ? noteTextarea.closest(".form-group") : null;
  const toggleDetailsButton = document.querySelector(".toggle-details");
  const operationDetails = document.querySelector("#operation-details");

  // ==========================
  //  UTILITY FUNCTIONS
  // ==========================

  function updateCountedBalance() {
    if (!countedBalanceEl) return;
    formatInputValue(countedBalanceEl);
    const inputVal = countedBalanceEl.value.replace(",", "."); // conversion pour pouvoir faire le calcul
    const total = Number(inputVal);
    const diff = total - theoreticalBalance;

    discrepancyEl.classList.remove("state-ok", "alert");

    if (!isNaN(diff)) {
      if (diff > 0) {
        discrepancyEl.classList.add("state-ok");
        discrepancyEl.value = "+" + formatNumber(diff);
      } else if (diff < 0) {
        discrepancyEl.classList.add("alert");
        discrepancyEl.value = formatNumber(diff);
      } else {
        discrepancyEl.classList.add("state-ok");
        discrepancyEl.value = "0,00";
      }
    } else {
      discrepancyEl.value = "";
    }
  }

  function setupToggleDetails() {
    operationDetails.classList.toggle("hidden");
  }

  // ==========================
  //  EVENT LISTENERS
  // ==========================

  if (countedBalanceEl) {
    countedBalanceEl.addEventListener("input", updateCountedBalance);
  }

  if (noteButton && noteGroup) {
    noteButton.addEventListener("click", function () {
      noteGroup.classList.toggle("hidden");
    });
  }

  updateCountedBalance();
  if (toggleDetailsButton) {
    toggleDetailsButton.addEventListener("click", setupToggleDetails);
  }
});
