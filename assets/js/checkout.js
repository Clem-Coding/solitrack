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
  const keepChangeButton = document.querySelector(".keep-change-button");
  const keepChangeInput = document.querySelector(".keep-change-input");

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
    console.log(remaining);
    updateRemainingUI(remaining);
  }

  function addPaymentInput(amount, method) {
    const inputGroup = document.createElement("li");
    inputGroup.classList.add("payment-group");

    const label = document.createElement("label");
    label.textContent = method === "card" ? "Carte Bleue" : "Espèces";

    const paymentInput = document.createElement("input");
    paymentInput.type = "text";
    // paymentInput.inputMode = "numeric";
    paymentInput.classList.add("payment-input");

    // console.log("l'input avant : ", paymentInput);
    console.log("Type de amount : ", typeof amount); // Vérifie la valeur avant la conversion
    const amountAsNumber = Number(amount);
    console.log("Après conversion : ", amountAsNumber); // Vérifie la valeur après conversion
    paymentInput.value = amountAsNumber;
    console.log(paymentInput.value);
    console.log("l'input après : ", paymentInput);
    paymentInput.min = 0;

    if (method === "card") {
      paymentInput.name = "card_amount"; // Pas de crochets []
    } else if (method === "cash") {
      paymentInput.name = "cash_amount"; // Pas de crochets []
    }
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

  function createWarningMessageRemainingAmount() {
    console.log("message montant restant à définir");
    const warningMessageElement = document.createElement("p");

    warningMessageElement.classList.add("error-remaining-amount");
    warningMessageElement.classList.add("flash-error");

    warningMessageElement.textContent = "Le montant restant doit être réglé avant de finaliser la transaction.";
    return warningMessageElement;
  }

  function createWarningMessageOpenPricingAmount() {
    console.log("message keep change à définir");
    const warningMessageElement = document.createElement("p");

    warningMessageElement.classList.add("error-pwyw-amount");
    warningMessageElement.classList.add("flash-error");

    warningMessageElement.textContent = "Le montant de prix libre doit être réglé avant de finaliser la transaction";
    return warningMessageElement;
  }

  function preventTransactionSubmission(event) {
    const remainingWarningMsgElement = document.querySelector(".error-remaining-amount");
    const pwywWarningMsgElement = document.querySelector(".error-pwyw-amount");

    const remainingAmount = getRemainingAmount();
    const pwywAmountInput = document.querySelector(".pwyw-amount");
    // console.log("la payment", pwywAmountInput.value);

    if (remainingAmount > 0) {
      if (!remainingWarningMsgElement) {
        const warningRemainingMsg = createWarningMessageRemainingAmount();

        paymentForm.appendChild(warningRemainingMsg);
      }

      event.preventDefault();
    }

    let warningMessage;

    if (pwywAmountInput) {
      if (pwywAmountInput.value === "0" || pwywAmountInput.value === "" || pwywAmountInput.value === null) {
        event.preventDefault();

        if (!document.querySelector(".error-pwyw-amount")) {
          warningMessage = createWarningMessageOpenPricingAmount();
          console.log("Message d'avertissement créé :", warningMessage);
          paymentForm.appendChild(warningMessage);
        }
      }

      pwywAmountInput.addEventListener("input", () => {
        if (pwywAmountInput.value > 0) {
          const existingWarningMessage = document.querySelector(".error-pwyw-amount");
          if (existingWarningMessage) {
            existingWarningMessage.remove();
          }
        }
      });
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
      label.setAttribute("for", "pwyw_amount");
      label.textContent = "Le poids des articles en vrac fait moins de 1kg. Montant à payer en prix libre:";

      const pwywAmountInput = document.createElement("input");
      pwywAmountInput.type = "text";
      pwywAmountInput.name = "pwyw_amount";
      pwywAmountInput.classList.add("pwyw-amount");
      pwywAmountInput.placeholder = "Entrez un montant";
      console.log(pwywAmountInput);

      pwywAmountInput.addEventListener("input", () => {
        formatInputValue(pwywAmountInput);
        const remainingAmount = getRemainingAmount();
        const enteredAmount = Number(pwywAmountInput.value);

        if (enteredAmount >= remainingAmount) {
          remainingAmountElement.textContent = "0,00";
          // remainingAmountElement.dataset.initial = "0";
        } // else à continuer pour réafficher le prix initial
      });

      paymentForm.appendChild(label);
      paymentForm.appendChild(pwywAmountInput);
    }
  }

  function handleKeepChange() {
    if (remainingTitle.textContent === "Retour Monnaie : ") {
      const keepChangeAmount = Math.abs(getRemainingAmount());
      console.log(keepChangeAmount);
      keepChangeInput.value = keepChangeAmount;
      const messageElement = document.createElement("p");
      messageElement.textContent = `Vous avez bien gardé la monnaie de ${keepChangeAmount} €.`;
      paymentForm.appendChild(messageElement);
      remainingTitle.textContent = "Restant à payer :";
      remainingAmountElement.textContent = 0;
    } else {
      console.log("non non");
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

  keepChangeButton.addEventListener("click", handleKeepChange);
});
