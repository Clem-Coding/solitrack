document.addEventListener("DOMContentLoaded", () => {
  //CONSTANTS

  const remainingAmount = document.querySelector("#remaining");
  const paymentButtons = document.querySelectorAll("#payment-button");
  console.log(remainingAmount.textContent);

  //FUNCTIONS

  function updateRemainingAmount(method) {
    let amount = Number(remainingAmount.textContent);
    console.log(amount);
    if (method === "card") {
      amount -= amount;
    } else if (method === "cash") {
      amount -= amount;
    }
    remainingAmount.textContent = amount.toFixed(2);
  }

  //   EVENT LISTENERS

  paymentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      console.log("je clique");
      const paymentMethod = event.target.dataset.method;
      updateRemainingAmount(paymentMethod);
    });
  });
});
