import { formatNumber, formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // CONSTANTES
  const dataPrice = document.querySelectorAll(".data-price");
  const remainingAmountElement = document.querySelector(".remaining");
  const paymentButtons = document.querySelectorAll(".payment-button");
  const paymentsList = document.querySelector(".payments-list");
  const registerSaleButton = document.querySelector(".register-sale-button");
  const paymentForm = document.querySelector(".payment-form");
  const salesItems = document.querySelectorAll("article");

  // console.log(paymentForm);

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

  function formatRemainingAmount(amount) {
    remainingAmountElement.textContent = formatNumber(amount);
  }

  function getTotalPaid() {
    let totalPaid = 0;
    document.querySelectorAll(".payment-input").forEach((input) => {
      totalPaid += Number(input.value) || 0;
    });
    return totalPaid;
  }

  // ici on attribue au dataset initial -> la valeur total du panier
  remainingAmountElement.dataset.initial = remainingAmountElement.textContent;

  function updateTotalAmount() {
    const totalPaid = getTotalPaid();

    const initialTotal = Number(remainingAmountElement.dataset.initial);
    const remaining = Math.max(initialTotal - totalPaid, 0);
    formatRemainingAmount(remaining);
  }

  function updateRemainingText() {
    const totalPaid = getTotalPaid();
    const initialTotal = Number(remainingAmountElement.dataset.initial);
    const remaining = initialTotal - totalPaid;

    if (remaining < 0) {
      // Si le client a trop payé, afficher "Retour Monnaie" en vert
      remainingAmountElement.textContent = formatNumber(Math.abs(remaining));
      document.querySelector("p .remaining").previousSibling.textContent = "Retour Monnaie : ";
      document.querySelector("p .remaining").style.color = "green";
    } else {
      formatRemainingAmount(remaining);
      document.querySelector("p .remaining").previousSibling.textContent = "Restant à payer : ";
      document.querySelector("p .remaining").style.color = "red";
    }
  }

  function addPaymentInput(amount, method) {
    const inputGroup = document.createElement("li");
    inputGroup.classList.add("payment-group");

    const label = document.createElement("label");
    label.textContent = method === "card" ? "Carte Bleue" : "Espèces";

    const paymentInput = document.createElement("input");
    paymentInput.type = "text";
    paymentInput.classList.add("payment-input");

    paymentInput.value = amount;
    paymentInput.min = 0;

    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Supprimer";
    deleteButton.classList.add("delete-button");

    deleteButton.addEventListener("click", () => {
      inputGroup.remove();
      updateTotalAmount();
      updateRemainingText();
    });

    paymentInput.addEventListener("input", (event) => {
      formatInputValue(paymentInput);
      updateTotalAmount();
      updateRemainingText();
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

  function preventTransactionSubmission(event) {
    const remainingAmount = getRemainingAmount(); // Récupère le montant restant à payer

    if (remainingAmount > 0) {
      const warningMessage = document.createElement("p");
      warningMessage.textContent = "Le montant restant doit être réglé avant de finaliser la transaction.";

      paymentForm.appendChild(warningMessage);
      event.preventDefault();
    }
  }

  function checkUnlabeledItemsWeight() {
    let totalWeight = 0;

    salesItems.forEach((item) => {
      const category = item.dataset.category;
      const weight = item.dataset.weight;

      if (category === "Vêtements vrac" || category === "Autres articles vrac") {
        totalWeight += Number(weight);
      }
    });

    if (totalWeight < 1) {
      const label = document.createElement("label");
      label.setAttribute("for", "tip");
      label.textContent = "Montant à payer en prix libre:";

      const tipCustomerInput = document.createElement("input");
      tipCustomerInput.type = "text";
      tipCustomerInput.name = "tip";
      tipCustomerInput.id = "tip";
      tipCustomerInput.placeholder = "Entrez un montant";

      tipCustomerInput.addEventListener("input", () => {
        formatInputValue(tipCustomerInput);
        const remainingAmount = getRemainingAmount();
        const enteredAmount = Number(tipCustomerInput.value);
        console.log(enteredAmount);
        if (enteredAmount >= remainingAmount) {
          remainingAmountElement.textContent = "0,00";
          // remainingAmountElement.dataset.initial = "0";
        } // else à continuer pour réafficher le prix initial
      });

      paymentForm.appendChild(label);
      paymentForm.appendChild(tipCustomerInput);
    }
  }

  // EVENT LISTENERS

  paymentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const paymentMethod = event.target.dataset.method;
      handlePaymentSelection(paymentMethod);
    });
  });

  formatPrices(dataPrice);

  registerSaleButton.addEventListener("click", (event) => {
    preventTransactionSubmission(event);
    checkUnlabeledItemsWeight();
  });
});
