import { formatNumber } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // CONSTANTES
  const dataPrice = document.querySelectorAll(".data-price");
  const remainingAmountElement = document.querySelector(".remaining");
  const paymentButtons = document.querySelectorAll(".payment-button");
  const paymentsList = document.querySelector(".payments-list");

  // FONCTIONS

  function formatPrices(dataPrice) {
    dataPrice.forEach((data) => {
      const toNumber = Number(data.textContent.replace(",", "."));
      const formattedPrice = formatNumber(toNumber);
      data.textContent = formattedPrice;
    });
  }

  function getRemainingAmount() {
    return Number(remainingAmountElement.textContent.replace(",", "."));
  }

  function setRemainingAmount(amount) {
    remainingAmountElement.textContent = formatNumber(amount);
  }

  function getTotalPaid() {
    let totalPaid = 0;
    document.querySelectorAll(".payment-input").forEach((input) => {
      totalPaid += Number(input.value) || 0;
    });
    return totalPaid;
  }

  function updateTotalAmount() {
    const totalPaid = getTotalPaid();
    const initialTotal = Number(remainingAmountElement.dataset.initial.replace(",", "."));
    const remaining = Math.max(initialTotal - totalPaid, 0);
    setRemainingAmount(remaining);
  }

  function addPaymentInput(amount, method) {
    const inputGroup = document.createElement("li");
    inputGroup.classList.add("payment-group");

    const label = document.createElement("label");
    label.textContent = method === "card" ? "Carte Bleue" : "Espèces";

    const paymentInput = document.createElement("input");
    paymentInput.type = "number";
    paymentInput.classList.add("payment-input");
    paymentInput.value = amount;
    paymentInput.min = 0;

    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Supprimer";
    deleteButton.classList.add("delete-button");

    deleteButton.addEventListener("click", () => {
      inputGroup.remove();
      updateTotalAmount();
    });

    paymentInput.addEventListener("input", () => {
      updateTotalAmount();
    });

    inputGroup.appendChild(label);
    inputGroup.appendChild(paymentInput);
    inputGroup.appendChild(deleteButton);
    paymentsList.appendChild(inputGroup);

    updateTotalAmount();
  }

  function handlePaymentSelection(method) {
    let remaining = getRemainingAmount();

    if (remaining > 0) {
      addPaymentInput(remaining, method);
    }
  }

  remainingAmountElement.dataset.initial = remainingAmountElement.textContent.replace(",", ".");

  // EVENT LISTENERS
  paymentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const paymentMethod = event.target.dataset.method;
      handlePaymentSelection(paymentMethod);
    });
  });

  formatPrices(dataPrice);
});
