document.addEventListener("DOMContentLoaded", (event) => {
  // CONSTANTS
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categoryInput = document.getElementById("sales_item_categoryId");
  const errorMessage = document.getElementById("error-message");
  const form = document.querySelector("form");
  const weightInputWrapper = document.getElementById("weight-input");
  const priceInputWrapper = document.getElementById("price-input");
  const quantityInputWrapper = document.getElementById("quantity-input");
  const inputs = [
    weightInputWrapper.querySelector("input"),
    priceInputWrapper.querySelector("input"),
    quantityInputWrapper.querySelector("input"),
  ];
  const addCartButton = document.getElementById("add-cart-button");

  // FUNCTIONS

  function setCategory(category) {
    categoryInput.setAttribute("value", category);
  }

  function resetButtonColors(buttons) {
    buttons.forEach((button) => {
      button.style.backgroundColor = "";
    });
  }

  function handleButtonClick(event) {
    const clickedButton = event.target;
    setCategory(clickedButton.getAttribute("data-category"));
    resetButtonColors(buttons);
    clickedButton.style.backgroundColor = "#FFA500";
  }

  function handleFormSubmit(event) {
    if (!categoryInput.value) {
      event.preventDefault();
      errorMessage.style.display = "block";
    } else {
      errorMessage.style.display = "none";
    }
  }

  function handleCategoryChange() {
    const category = categoryInput.value;

    inputs.forEach((input) => {
      input.value = "";
    });

    addCartButton.classList.remove("hidden");
    priceInputWrapper.classList.add("hidden");
    quantityInputWrapper.classList.add("hidden");
    weightInputWrapper.classList.add("hidden");

    if (category === "1" || category === "2") {
      weightInputWrapper.classList.remove("hidden");
    } else if (category === "3") {
      weightInputWrapper.classList.remove("hidden");
      priceInputWrapper.classList.remove("hidden");
    } else if (category === "4") {
      quantityInputWrapper.classList.remove("hidden");
    }
  }

  // EVENT LISTENERS

  buttons.forEach((button) => {
    button.addEventListener("click", (event) => {
      handleButtonClick(event);
      handleCategoryChange();
    });
  });

  form.addEventListener("submit", handleFormSubmit);

  //LOGS
});
