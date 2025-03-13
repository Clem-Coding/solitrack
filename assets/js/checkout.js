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
  const remainingTitle = document.querySelector(".remaining-title");

  //INITIALIZE
  // ici on attribue au dataset initial -> la valeur total du panier
  remainingAmountElement.dataset.initial = remainingAmountElement.textContent;
  remainingTitle.style.color = "red";
  remainingAmountElement.style.color = "red";

  // FONCTIONS

  function formatPrices(dataPrice) {
    dataPrice.forEach((data) => {
      const toNumber = Number(data.textContent);
      const formattedPrice = formatNumber(toNumber);
      data.textContent = formattedPrice;
    });
  }

  function getRemainingAmount() {
    return Number(remainingAmountElement.textContent.replace(",", "."));
  }

  // function formatRemainingAmount(amount) {
  //   remainingAmountElement.textContent = formatNumber(amount);
  // }

  function getTotalPaid() {
    const paymentInputs = document.querySelectorAll(".payment-input");
    let totalPaid = 0;
    paymentInputs.forEach((input) => {
      totalPaid += Number(input.value) || 0;
    });
    return totalPaid;
  }

  function getRemainingAmount() {
    const totalPaid = getTotalPaid();
    const initialTotal = Number(remainingAmountElement.dataset.initial);
    const remaining = initialTotal - totalPaid;

    // Arrondir à 2 décimales pour éviter les petites erreurs d'arrondi
    return Math.round(remaining * 100) / 100;
  }

  function updateRemainingUI(remaining) {
    const isOverpaid = remaining < 0;
    const amount = formatNumber(Math.abs(remaining));
    const text = isOverpaid ? "Retour Monnaie : " : "Restant à payer : ";

    let color = "red";
    if (remaining === 0) {
      color = "green";
    } else if (isOverpaid) {
      color = "green";
    }

    remainingAmountElement.textContent = amount;
    remainingTitle.textContent = text;
    remainingAmountElement.dataset.status = isOverpaid ? "overpaid" : "remaining";
    remainingTitle.style.color = color;
    remainingAmountElement.style.color = color;
  }

  function updateAmounts() {
    const remaining = getRemainingAmount();
    updateRemainingUI(remaining);
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
      updateAmounts();
      // updateRemainingText();
    });

    paymentInput.addEventListener("input", (event) => {
      formatInputValue(paymentInput);
      updateAmounts();
      // updateRemainingText();
    });

    inputGroup.appendChild(label);
    inputGroup.appendChild(paymentInput);
    inputGroup.appendChild(deleteButton);
    paymentsList.appendChild(inputGroup);

    updateAmounts();
  }

  function handlePaymentSelection(method) {
    let remaining = getRemainingAmount();

    if (remaining > 0) {
      addPaymentInput(remaining, method);
    }
  }

  function preventTransactionSubmission(event) {
    const remainingAmount = getRemainingAmount();

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
      let price = item.dataset.price;

      if (category === "Vêtements vrac" || category === "Autres articles vrac") {
        totalWeight += Number(weight);
        price = "0";
      }
    });

    if (totalWeight < 1) {
      const label = document.createElement("label");
      label.setAttribute("for", "tip");
      label.textContent = "Le poids des articles en vrac fait moins de 1kg. Montant à payer en prix libre:";

      const tipCustomerInput = document.createElement("input");
      tipCustomerInput.type = "text";
      tipCustomerInput.name = "tip";
      tipCustomerInput.id = "tip";
      tipCustomerInput.placeholder = "Entrez un montant";

      tipCustomerInput.addEventListener("input", () => {
        formatInputValue(tipCustomerInput);
        const remainingAmount = getRemainingAmount();
        const enteredAmount = Number(tipCustomerInput.value);

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

  // updateAmounts();

  paymentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const paymentMethod = event.target.dataset.method;
      handlePaymentSelection(paymentMethod);
    });
  });

  formatPrices(dataPrice);

  registerSaleButton.addEventListener("click", (event) => {
    preventTransactionSubmission(event);
    localStorage.removeItem("cart");
  });

  checkUnlabeledItemsWeight();
});
