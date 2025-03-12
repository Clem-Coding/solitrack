import { formatNumber } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // CONSTANTS

  const dataPrice = document.querySelectorAll(".data-price");
  const remainingAmount = Number(document.querySelector(".remaining").textContent);
  const paymentButtons = document.querySelectorAll(".payment-button");
  const paymentsList = document.querySelector(".payments-list");

  console.log(paymentsList);

  // FUNCTIONS

  function formatPrices(dataPrice) {
    dataPrice.forEach((data) => {
      const toNumber = Number(data.textContent);
      const formattedPrice = formatNumber(toNumber);
      data.textContent = formattedPrice;
    });
  }

  function handlePaymentSelection(method) {
    let amount = remainingAmount;

    if (method === "card" || method === "cash") {
      addPaymentInput(amount);
    }

    updateTotalAmount();
  }

  function addPaymentInput(amount) {
    const inputGroup = document.createElement("li");

    // inputGroup.classList.add("payment-input-group");
    const labelInput = document.createElement("label");
    const paymentInput = document.createElement("input");
    paymentInput.type = "number";

    console.log("la value", paymentInput.value);
    paymentInput.classList.add("payment-input");
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Supprimer";
    deleteButton.classList.add("delete-button");

    inputGroup.appendChild(paymentInput);
    inputGroup.appendChild(deleteButton);
    paymentsList.appendChild(inputGroup);
  }

  function updateTotalAmount() {
    console.log("update");
  }

  // EVENT LISTENERS
  paymentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      console.log("helloi!");
      const paymentMethod = event.target.dataset.method;
      handlePaymentSelection(paymentMethod);
    });
  });

  formatPrices(dataPrice);
});
