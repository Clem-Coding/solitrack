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
  console.log(salesItems);
  const remainingTitle = document.querySelector(".remaining-title");
  const remainingPriceElement = document.querySelector(".remaining-price");
  const mailInputGroup = document.querySelector("#email").closest(".form-group");
  const receiptButton = document.querySelector(".receipt-button");
  const pwywAmountInput = document.querySelector("#pwyw_amount");

  const toggle = document.getElementById("change-amount-toggle");
  toggle.disabled = true;

  // ==========================

  //here we set the initial total amount of the cart to the dataset initial attribute
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

  function getTotalPaid() {
    const paymentInputs = document.querySelectorAll(".payment-input");

    let totalPaid = 0;

    //  Calculates the total of all payment methods and adds it to the total paid
    paymentInputs.forEach((input) => {
      totalPaid += Number(input.value.replace(",", ".")) || 0;
    });

    return totalPaid;
  }

  //_______________________ RULE FOR PAY-WHAT-YOU-WANT PRICING OF BULK ITEMS UNDER 1KG__________________________________

  function isBulkCategory(category) {
    return category === "Vêtements vrac" || category === "Autres articles vrac";
  }

  function isLabeledCategory(category) {
    return category === "Boisson" || category === "Article étiqueté" || category === "Livre";
  }

  function checkUnlabeledItemsWeight() {
    let totalWeight = 0;
    let hasBulkItem = false;

    //Retrieve the data-attributes of the cart items
    salesItems.forEach(({ dataset }) => {
      const { category, weight } = dataset;

      // If there are no labeled items, then there is at least one bulk item
      if (!isLabeledCategory(category)) {
        hasBulkItem = true;
      }

      //We only increment the total weight with bulk items
      if (isBulkCategory(category)) {
        totalWeight += Number(weight);
      }
    });

    //Check if the total weight is less than 1kg and there is at least one bulk item
    if (totalWeight < 1 && hasBulkItem) {
      pwywAmountInput.parentElement.classList.remove("hidden");

      pwywAmountInput.addEventListener("input", () => {
        formatInputValue(pwywAmountInput);
        updateAmounts();
      });
    }
  }

  //_________________________RETRIEVE THE REMAINING AMOUNT TO BE PAID___________________________________________________
  function getRemainingAmount() {
    const totalPaid = getTotalPaid();
    let initialTotal = Number(remainingNumberElement.dataset.initial);

    // If there is a pay-what-you-want input, we add its value to the initial total
    if (!pwywAmountInput.parentElement.classList.contains("hidden")) {
      const pwywAmount = Number(pwywAmountInput.value.replace(",", ".")) || 0;
      initialTotal += pwywAmount;
    }

    //  The remaining total will be negative if the price paid is greater than the initial amount
    const remainingAmount = initialTotal - totalPaid;

    //Round the remaining amount to 2 decimal places
    return Math.round(remainingAmount * 100) / 100;
  }

  //________________// UPDATE TEXT AND COLORS FOR REMAINING TOTAL AND BALANCE VALUE_____________________________________

  function updateRemainingUI(remaining) {
    // Evaluates to true if remaining is negative (see function getRemainingAmount)
    let isOverpaid = remaining < 0;

    // We use Math.abs to return the absolute value (if the remaining amount is negative, it becomes positive for the change to give back)
    let balance = formatNumber(Math.abs(remaining));

    const text = isOverpaid ? "Retour Monnaie : " : "Restant à payer : ";

    let statusClass = "alert";

    if (remaining === 0 || isOverpaid) {
      statusClass = "state-ok";
    }

    remainingTitle.classList.remove("alert", "state-ok");
    remainingPriceElement.classList.remove("alert", "state-ok");

    remainingNumberElement.textContent = balance;
    remainingTitle.textContent = text;
    remainingNumberElement.dataset.status = isOverpaid ? "overpaid" : "remaining";
    remainingTitle.classList.add(statusClass);
    remainingPriceElement.classList.add(statusClass);

    // 🔁 Enables/disables the toggle depending on the change to give back
    if (toggle) {
      if (isOverpaid) {
        toggle.disabled = false;
      } else {
        toggle.disabled = true;
        toggle.checked = false;
      }
    }
  }

  function updateAmounts() {
    const remaining = getRemainingAmount();
    updateRemainingUI(remaining);
  }

  //_________________________ ADDING PAYMENT INPUTS FOR CARD OR CASH____________________________________________________

  function createLabel(method) {
    const label = document.createElement("label");
    label.textContent = method === "card" ? "Carte Bleue" : "Espèces";
    return label;
  }

  function createInput(amount, method) {
    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("payment-input");
    input.value = Number(amount);
    input.min = 0;
    // input.name = method === "card" ? "card_amount" : "cash_amount";
    input.name = method === "card" ? "card_amount[]" : "cash_amount[]";

    return input;
  }

  function createDeleteButton(onDelete) {
    const button = document.createElement("button");
    const icon = document.createElement("i");
    icon.classList.add("ph", "ph-x-circle");
    button.appendChild(icon);
    button.classList.add("btn-cross-delete");
    button.addEventListener("click", onDelete);
    return button;
  }

  function addPaymentInput(amount, method) {
    const existingMsg = document.querySelector(".error-remaining-amount");
    if (existingMsg) existingMsg.remove();

    const inputGroup = document.createElement("li");
    inputGroup.classList.add("payment-group");

    const paymentDetailsContainer = document.createElement("div");
    paymentDetailsContainer.classList.add("payment-details-container");

    const label = createLabel(method);
    const paymentInput = createInput(amount, method);
    const deleteButton = createDeleteButton(() => {
      inputGroup.remove();
      updateAmounts();
    });

    paymentInput.addEventListener("input", () => {
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

  //_______________________________ ALERT MESSAGES FOR THE USER_________________________________________________________

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

    // ADDING THE MESSAGE FOR THE REMAINING AMOUNT TO PAY
    const remainingAmount = getRemainingAmount();
    if (remainingAmount > 0) {
      if (!remainingWarningMsgElement) {
        const warningRemainingMsg = createWarningMessageRemainingAmount();

        paymentForm.appendChild(warningRemainingMsg);
      }

      event.preventDefault();
    }

    //  ADDING THE MESSAGE FOR THE PAY-WHAT-YOU-WANT AMOUNT TO PAY
    let warningMessage;

    if (pwywAmountInput && !pwywAmountInput.parentElement.classList.contains("hidden")) {
      if (pwywAmountInput.value === "0" || pwywAmountInput.value === "" || pwywAmountInput.value === null) {
        event.preventDefault();

        if (!document.querySelector(".error-pwyw-amount")) {
          pwywAmountInput.classList.add("input-error");
          warningMessage = createWarningMessageOpenPricingAmount();
          paymentForm.appendChild(warningMessage);
        }
      }

      // CHECK IF PAY-WHAT-YOU-WANT VALUE IS SET TO REMOVE ERROR MESSAGE
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

  // ==========================
  // 🔧 HANDLE FUNCTIONS
  // ==========================

  function handleKeepChangeOnSubmit() {
    // const toggle = document.getElementById("change-amount-toggle");

    const hiddenInput = document.getElementById("change-amount");
    const remaining = getRemainingAmount(); // ex: -0.50

    if (!toggle || !hiddenInput) return;

    const changeAmount = toggle.checked
      ? Math.abs(remaining) // garder la monnaie → positif
      : remaining; // rendre la monnaie → négatif

    hiddenInput.value = changeAmount;
  }

  function handlePaymentSelection(method) {
    let remaining = getRemainingAmount();

    if (
      !pwywAmountInput.parentElement.classList.contains("hidden") &&
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
      if (remaining > 0) {
        addPaymentInput(remaining, method);
      }
    }
  }

  // ==========================
  // FETCH CITIES - API GOUV
  // ==========================
  const zipcodeInput = document.getElementById("zipcode");
  const citySelect = document.getElementById("city-select");

  async function fetchCitiesByZip(zipcode) {
    if (!/^\d{5}$/.test(zipcode)) {
      citySelect.innerHTML = '<option value="">Code postal invalide</option>';
      return;
    }

    try {
      const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${zipcode}&type=municipality&limit=20`);
      const data = await response.json();

      const cities = data.features.map((f) => f.properties.city).filter((v, i, a) => v && a.indexOf(v) === i);
      if (cities.length === 0) {
        citySelect.innerHTML = '<option value="">Aucune ville trouvée</option>';
      } else {
        citySelect.innerHTML = cities.map((city) => `<option value="${city}">${city}</option>`).join("");
      }
    } catch (err) {
      console.error(err);
      citySelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
    }
  }

  zipcodeInput.addEventListener("blur", () => {
    const zip = zipcodeInput.value.trim();
    fetchCitiesByZip(zip);
  });

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

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
    handleKeepChangeOnSubmit();
  });

  checkUnlabeledItemsWeight();

  receiptButton.addEventListener("click", function () {
    mailInputGroup.classList.toggle("hidden");
  });
});
