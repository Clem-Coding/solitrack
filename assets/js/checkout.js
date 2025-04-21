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
  console.log(remainingPriceElement);

  //INITIALIZE
  // ici on attribue au dataset initial -> la valeur total du panier
  remainingNumberElement.dataset.initial = remainingNumberElement.textContent;
  // remainingTitle.style.color = "red";
  // remainingNumberElement.style.color = "red";
  remainingTitle.classList.add("alert");
  remainingPriceElement.classList.add("alert");

  // FONCTIONS

  function formatPrices(dataPrice) {
    dataPrice.forEach((data) => {
      const toNumber = Number(data.textContent);
      const formattedPrice = formatNumber(toNumber);
      data.textContent = formattedPrice;
    });
  }

  // function getRemainingAmount() {
  //   return Number(remainingNumberElement.textContent.replace(",", "."));
  // }

  // function formatRemainingAmount(amount) {
  //   remainingNumberElement.textContent = formatNumber(amount);
  // }

  //RECUPERER LE TOTAL PAYÉ
  function getTotalPaid() {
    const paymentInputs = document.querySelectorAll(".payment-input");
    let totalPaid = 0;
    paymentInputs.forEach((input) => {
      totalPaid += Number(input.value.replace(",", ".")) || 0;
    });
    return totalPaid;
  }

  //________________________RECUPERER LE MONTANT RESTANT A  PAYER_______________________________________________________
  function getRemainingAmount() {
    const totalPaid = getTotalPaid();
    const initialTotal = Number(remainingNumberElement.dataset.initial);

    //le total restant sera négatif si le prix payé est supérieur on montant initial
    const remaining = initialTotal - totalPaid;

    // Arrondir à 2 décimales pour éviter les petites erreurs d'arrondi
    return Math.round(remaining * 100) / 100;
  }

  //________METTRE A JOUR LE TEXT ET LES COULEURS POUR LE TOTAL RESTANT AINSI QUE LA VALEUR DU SOLDE____________________
  function updateRemainingUI(remaining) {
    //équivaut à true si le remaining est négatif (cf function getRemainingAmount)
    const isOverpaid = remaining < 0;

    // on met Math.abs pour renvoyer la valeur absolue (si le restant est négatif, il devient positif pour la monnaie à rendre)
    const balance = formatNumber(Math.abs(remaining));
    console.log("le montant à rendre ou restant", balance);
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
    const inputGroup = document.createElement("li");
    inputGroup.classList.add("payment-group");
    console.log("l'amount", amount);

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

    // Ajout de la div "payment-details-container" et du bouton dans le groupe
    inputGroup.appendChild(paymentDetailsContainer);
    inputGroup.appendChild(deleteButton);

    // Ajout de l'inputGroup à la liste des paiements
    paymentsList.appendChild(inputGroup);

    updateAmounts();
  }

  function handlePaymentSelection(method) {
    let remaining = getRemainingAmount();

    let pwywAmountInput = document.querySelector(".pwyw-amount");
    // console.log("la value", pwywAmountInput.value);

    if (remaining > 0) {
      if (pwywAmountInput) {
        let total = remaining + Number(pwywAmountInput.value);
        console.log("total du remaininge + pwyw", total);
        addPaymentInput(total, method);
      } else {
        addPaymentInput(remaining, method);
      }
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
      // let price = item.dataset.price;
      // console.log("price", price);

      if (category === "Vêtements vrac" || category === "Autres articles vrac") {
        totalWeight += Number(weight);
        console.log("le total weight", totalWeight);
        if (totalWeight < 1) {
          const label = document.createElement("label");
          label.setAttribute("for", "pwyw_amount");
          label.textContent = "Le poids des articles en vrac fait moins de 1kg. Montant à payer en prix libre:";

          const pwywAmountInput = document.createElement("input");
          pwywAmountInput.type = "text";
          pwywAmountInput.name = "pwyw_amount";
          pwywAmountInput.classList.add("pwyw-amount");
          pwywAmountInput.placeholder = "Entrez un montant";
          // console.log(pwywAmountInput);

          pwywAmountInput.addEventListener("input", () => {
            formatInputValue(pwywAmountInput);

            let remainingAmount = getRemainingAmount();
            console.log("le reminaing Amount", remainingAmount);
            const pwywAmount = Number(pwywAmountInput.value);
            remainingAmount += pwywAmount;
            remainingNumberElement.textContent = remainingAmount;
            console.log("le reminaing Amount après", remainingAmount);
            // remainingNumberElement.textContent += pwywAmount;

            console.log("le pwyw amount", pwywAmount);
            if (pwywAmount >= remainingAmount) {
              // remainingNumberElement.dataset.initial = "0";
            } else {
              //
            }
          });

          paymentForm.appendChild(label);
          paymentForm.appendChild(pwywAmountInput);
        }
      }
    });
  }

  function handleKeepChange() {
    if (remainingTitle.textContent === "Retour Monnaie : ") {
      const keepChangeAmount = Math.abs(getRemainingAmount());
      // console.log(keepChangeAmount);
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
