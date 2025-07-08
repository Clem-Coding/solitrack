import { formatNumber, formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  const countedBalanceEl = document.querySelector("#cash_register_closure_countedBalance");
  const discrepancyEl = document.querySelector("#cash_register_closure_discrepancy");
  const inputs = document.querySelectorAll('input[name^="coin_count[coin_"]');
  const theoreticalBalanceEl = document.querySelector("#theoreticalBalance");
  const theoreticalBalance = theoreticalBalanceEl ? Number(theoreticalBalanceEl.textContent) : 0;

  const noteButton = document.querySelector(".note-toggle");
  const noteGroup = document.querySelector(".form-group textarea#cash_register_closure_note").closest(".form-group");

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================

  function parseCoinValue(name) {
    // Extraire ce qui est entre crochets
    const match = name.match(/\[(.+?)\]/);
    if (!match) return NaN; // si pas trouvé

    let val = match[1].replace("coin_", "");
    if (val.includes("_")) {
      val = val.replace(/_/g, ".");
    }

    return Number(val);
  }

  function updateCountedBalance() {
    let total = 0;

    inputs.forEach((input) => {
      const value = parseCoinValue(input.name);
      const quantity = Number(input.value) || 0;
      total += value * quantity;
    });

    countedBalanceEl.value = formatNumber(total);
    const diff = total - theoreticalBalance;
    console.log(diff);

    discrepancyEl.classList.remove("state-ok", "alert");

    if (diff >= 0) {
      discrepancyEl.classList.add("state-ok");
      discrepancyEl.value = diff > 0 ? "+" + formatNumber(diff) : formatNumber(diff);
    } else {
      discrepancyEl.classList.add("alert");
      discrepancyEl.value = formatNumber(diff);
    }
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

  inputs.forEach((input) => input.addEventListener("input", updateCountedBalance));

  noteButton.addEventListener("click", function () {
    noteGroup.classList.toggle("hidden");
  });

  updateCountedBalance();
});
