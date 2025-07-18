import { formatNumber, formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  const countedBalanceEl = document.querySelector("#cash_register_closure_countedBalance");
  const discrepancyEl = document.querySelector("#cash_register_closure_discrepancy");

  const theoreticalBalanceEl = document.querySelector("#theoreticalBalance");
  const theoreticalBalance = theoreticalBalanceEl ? Number(theoreticalBalanceEl.textContent) : 0;

  const noteButton = document.querySelector(".note-toggle");
  const noteGroup = document.querySelector(".form-group textarea#cash_register_closure_note").closest(".form-group");

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================

  function updateCountedBalance() {
    formatInputValue(countedBalanceEl);
    const inputVal = countedBalanceEl.value.replace(",", "."); // conversion pour pouvoir faire le calcul
    const total = Number(inputVal);
    const diff = total - theoreticalBalance;

    discrepancyEl.classList.remove("state-ok", "alert");

    if (!isNaN(diff)) {
      if (diff >= 0) {
        discrepancyEl.classList.add("state-ok");
        discrepancyEl.value = "+" + formatNumber(diff);
      } else {
        discrepancyEl.classList.add("alert");
        discrepancyEl.value = formatNumber(diff);
      }
    } else {
      discrepancyEl.value = "";
    }
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

  countedBalanceEl.addEventListener("input", updateCountedBalance);

  noteButton.addEventListener("click", function () {
    noteGroup.classList.toggle("hidden");
  });

  // updateCountedBalance();
});
