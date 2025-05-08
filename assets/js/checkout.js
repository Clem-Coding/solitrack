import { formatNumber, formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // CONSTANTES
  const dataPrice = document.querySelectorAll(".data-price");
  const remainingNumberElement = document.querySelector(".remaining");
  const paymentButtons = document.querySelectorAll(".payment-button");
  const paymentsList = document.querySelector(".payments-list");
  const registerSaleButton = document.querySelector(".register-sale-button");
  const paymentForm = document.querySelector(".payment-form");
  const salesItems = document.querySelectorAll("article");
  const remainingTitle = document.querySelector(".remaining-title");
  const keepChangeButton = document.querySelector(".keep-change-button");
  const keepChangeInput = document.querySelector(".keep-change-input");
  const remainingPriceElement = document.querySelector(".remaining-price");
  const mailInputGroup = document.querySelector("#email").closest(".form-group");
  const receiptButton = document.querySelector(".receipt-button");
  const paymentMethodElement = document.querySelector("#payment-method");
  const pwywAmountInput = document.querySelector("#pwyw_amount");

  // const emailInput = document.querySelector("#email");

  //INITIALIZE
  // ici on attribue au dataset initial -> la valeur total du panier
  remainingNumberElement.dataset.initial = remainingNumberElement.textContent;
  remainingTitle.classList.add("alert");
  remainingPriceElement.classList.add("alert");

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================

  function formatPrices(dataPrice) {
    dataPrice.forEach((data) => {
      const toNumber = Number(data.textContent);
      const formattedPrice = formatNumber(toNumber);
      data.textContent = formattedPrice;
    });
  }

  //RECUPERER LE TOTAL PAYÉ
  function getTotalPaid() {
    // récupère tous les input des moyens de paiements ajoutés
    const paymentInputs = document.querySelectorAll(".payment-input");

    let totalPaid = 0;

    //fait le total de tous les moyens de paiments et l'ajoute au total payé
    paymentInputs.forEach((input) => {
      totalPaid += Number(input.value.replace(",", ".")) || 0;
    });

    return totalPaid;
  }

  //________________________RECUPERER LE MONTANT RESTANT A  PAYER_______________________________________________________
  function getRemainingAmount() {
    const totalPaid = getTotalPaid();
    let initialTotal = Number(remainingNumberElement.dataset.initial);

    // Si input de prix libre visible
    if (!pwywAmountInput.parentElement.classList.contains("hidden")) {
      //récupère la valeur du prix libre
      const pwywAmount = Number(pwywAmountInput.value.replace(",", ".")) || 0;

      //L'ajoute au prix initial qui est le prix donné par le panier venant du back
      initialTotal += pwywAmount;
    }

    //le total restant sera négatif si le prix payé est supérieur au montant initial
    const remainingAmount = initialTotal - totalPaid;
    console.log(
      "le VRAI RESTANT A PAYER EN TEMPS REEL et en fonction des moyens de paiement ajoutés ou modifiés",
      remainingAmount
    );
    // Arrondir à 2 décimales pour éviter les petites erreurs d'arrondi
    return Math.round(remainingAmount * 100) / 100;
  }

  //_______________________REGLE POUR LE PRIX LIBRE DES ARTICLES VRAC DE MOINS DE 1KG__________________________________

  function checkUnlabeledItemsWeight() {
    let totalWeight = 0;
    let onlyDrinks = true;

    salesItems.forEach((item) => {
      const category = item.dataset.category;
      const weight = item.dataset.weight;

      if (category !== "Boisson") {
        onlyDrinks = false;
      }

      if (category === "Vêtements vrac" || category === "Autres articles vrac") {
        totalWeight += Number(weight);
        console.log("le total weight", totalWeight);
      }
    });

    if (totalWeight < 1 && !onlyDrinks) {
      pwywAmountInput.parentElement.classList.remove("hidden");

      pwywAmountInput.addEventListener("input", () => {
        formatInputValue(pwywAmountInput);
        updateAmounts();
      });
    }
  }

  //________METTRE A JOUR LE TEXT ET LES COULEURS POUR LE TOTAL RESTANT AINSI QUE LA VALEUR DU SOLDE____________________
  function updateRemainingUI(remaining) {
    //équivaut à true si le remaining est négatif (cf function getRemainingAmount)
    let isOverpaid = remaining < 0;

    // on met Math.abs pour renvoyer la valeur absolue (si le restant est négatif, il devient positif pour la monnaie à rendre)
    let balance = formatNumber(Math.abs(remaining));

    const text = isOverpaid ? "Retour Monnaie : " : "Restant à payer : ";

    let statusClass = "alert";
    if (remaining === 0) {
      statusClass = "state-ok";
    } else if (isOverpaid) {
      statusClass = "state-ok";
    }

    remainingTitle.classList.remove("alert", "state-ok");
    remainingPriceElement.classList.remove("alert", "state-ok");

    remainingNumberElement.textContent = balance;
    remainingTitle.textContent = text;
    remainingNumberElement.dataset.status = isOverpaid ? "overpaid" : "remaining";
    remainingTitle.classList.add(statusClass);
    remainingPriceElement.classList.add(statusClass);
  }

  function updateAmounts() {
    const remaining = getRemainingAmount();
    updateRemainingUI(remaining);
  }

  function addPaymentInput(amount, method) {
    const existingMsg = document.querySelector(".error-remaining-amount");
    if (existingMsg) {
      existingMsg.remove();
    }

    const inputGroup = document.createElement("li");
    inputGroup.classList.add("payment-group");

    const paymentDetailsContainer = document.createElement("div");
    paymentDetailsContainer.classList.add("payment-details-container");

    const label = document.createElement("label");
    label.textContent = method === "card" ? "Carte Bleue" : "Espèces";

    const paymentInput = document.createElement("input");
    paymentInput.type = "text";
    paymentInput.classList.add("payment-input");
    const amountAsNumber = Number(amount);
    paymentInput.value = amountAsNumber;
    paymentInput.min = 0;

    if (method === "card") {
      paymentInput.name = "card_amount"; // Pas de crochets []
    } else if (method === "cash") {
      paymentInput.name = "cash_amount"; // Pas de crochets []
    }
    const deleteButton = document.createElement("button");
    deleteButton.innerHTML = '<i class="ph ph-x-circle"></i>';

    deleteButton.classList.add("btn-cross-delete");

    deleteButton.addEventListener("click", () => {
      inputGroup.remove();
      updateAmounts();
    });

    paymentInput.addEventListener("input", (event) => {
      formatInputValue(paymentInput);
      updateAmounts();
    });

    paymentDetailsContainer.appendChild(label);
    paymentDetailsContainer.appendChild(paymentInput);

    inputGroup.appendChild(paymentDetailsContainer);
    inputGroup.appendChild(deleteButton);

    paymentsList.appendChild(inputGroup);

    updateAmounts();
  }

  function handlePaymentSelection(method) {
    let remaining = getRemainingAmount();

    if (
      pwywAmountInput &&
      (pwywAmountInput.value.trim() === "" || Number(pwywAmountInput.value.replace(",", ".")) === 0)
    ) {
      pwywAmountInput.classList.add("input-error");

      pwywAmountInput.addEventListener("input", () => {
        const value = Number(pwywAmountInput.value.replace(",", "."));
        if (value > 0) {
          pwywAmountInput.classList.remove("input-error");
        }
      });
    } else {
      addPaymentInput(remaining, method);
    }
  }

  function createWarningMessageRemainingAmount() {
    const warningMessageElement = document.createElement("p");

    warningMessageElement.classList.add("error-remaining-amount");
    warningMessageElement.classList.add("flash-error");

    warningMessageElement.textContent = "Le montant restant doit être réglé avant de finaliser la transaction.";
    return warningMessageElement;
  }

  function createWarningMessageOpenPricingAmount() {
    const warningMessageElement = document.createElement("p");

    warningMessageElement.classList.add("error-pwyw-amount");
    warningMessageElement.classList.add("flash-error");

    warningMessageElement.textContent = "Le montant de prix libre doit être réglé avant de finaliser la transaction";
    return warningMessageElement;
  }

  function preventTransactionSubmission(event) {
    const remainingWarningMsgElement = document.querySelector(".error-remaining-amount");
    // const pwywWarningMsgElement = document.querySelector(".error-pwyw-amount");

    // AJOUT DU MESSAGE POUR LE MONTANT RESTANT A PAYER
    const remainingAmount = getRemainingAmount();
    if (remainingAmount > 0) {
      if (!remainingWarningMsgElement) {
        const warningRemainingMsg = createWarningMessageRemainingAmount();

        paymentForm.appendChild(warningRemainingMsg);
      }

      event.preventDefault();
    }

    // AJOUT DU MESSAGE POUR LE PRIX LIBRE A PAYER
    let warningMessage;

    if (pwywAmountInput && !pwywAmountInput.parentElement.classList.contains("hidden")) {
      if (pwywAmountInput.value === "0" || pwywAmountInput.value === "" || pwywAmountInput.value === null) {
        event.preventDefault();

        //on rajoute le message d'erreur
        if (!document.querySelector(".error-pwyw-amount")) {
          pwywAmountInput.classList.add("input-error");
          warningMessage = createWarningMessageOpenPricingAmount();
          paymentForm.appendChild(warningMessage);
        }
      }

      // Vérifier en temps réel que le valeur de prix libre est bien ajoutée afin de remove le messae d'erreur
      pwywAmountInput.addEventListener("input", () => {
        if (Number(pwywAmountInput.value.replace(",", ".")) > 0) {
          const existingWarningMessage = document.querySelector(".error-pwyw-amount");
          if (existingWarningMessage) {
            pwywAmountInput.classList.remove("input-error");
            existingWarningMessage.remove();
          }
        }
      });
    }
  }

  function handleKeepChange() {
    if (remainingTitle.textContent === "Retour Monnaie : ") {
      const keepChangeAmount = Math.abs(getRemainingAmount());
      keepChangeInput.value = keepChangeAmount;
      const messageElement = document.createElement("p");
      messageElement.classList.add("flash-success");
      messageElement.textContent = `Vous avez bien gardé la monnaie de ${keepChangeAmount} €.`;
      paymentForm.appendChild(messageElement);
      remainingTitle.textContent = "Restant à payer :";
      remainingNumberElement.textContent = 0;
    } else {
      console.log("non non");
    }
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

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

  receiptButton.addEventListener("click", function () {
    mailInputGroup.classList.toggle("hidden");
  });
});
