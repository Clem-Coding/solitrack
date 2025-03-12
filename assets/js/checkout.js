import { formatNumber } from "./utils";

document.addEventListener("DOMContentLoaded", () => {
  //CONSTANTS

  const remainingAmount = document.querySelector("#remaining");
  const paymentButtons = document.querySelectorAll("#payment-button");
  console.log(remainingAmount.textContent);

  //FUNCTIONS
  function handlePaymentSelection(method) {
    let amount = Number(remainingAmount.textContent);

    if (method === "card" || method === "cash") {
      addPaymentInput(amount);
    }

    updateTotalAmount();
  }

  function addPaymentInput(amount) {
    console.log("addPayment");
    const inputGroup = document.createElement("div");

    inputGroup.classList.add("payment-input-group");
    const paymentInput = document.createElement("input");

    paymentInput.type = "number";
    paymentInput.value = amount.toFixed(2).replace(".", ",");
    console.log("la value", paymentInput.value);
    paymentInput.classList.add("payment-input");
    // const deleteButton = document.createElement("button");
    // deleteButton.textContent = "Supprimer";
    // deleteButton.classList.add("delete-button");
    // deleteButton.addEventListener("click", () => {
    //   paymentContainer.removeChild(inputGroup);
    //   updateTotalAmount();
    // });
    // inputGroup.appendChild(paymentInput);
    // inputGroup.appendChild(deleteButton);
    // paymentContainer.appendChild(inputGroup);
  }

  function updateTotalAmount() {
    console.log("update");
    // const paymentInputs = document.querySelectorAll(".payment-input");
    // let totalAmount = 0;

    // paymentInputs.forEach((input) => {
    //   totalAmount += Number(input.value);
    // });

    // remainingAmount.textContent = totalAmount.toFixed(2);
  }

  // EVENT LISTENERS
  paymentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const paymentMethod = event.target.dataset.method;
      handlePaymentSelection(paymentMethod);
    });
  });
});
