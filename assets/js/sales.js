document.addEventListener("DOMContentLoaded", (event) => {
  // CONSTANTS
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categoryInput = document.getElementById("sales_item_categoryId");
  const errorMessage = document.getElementById("error-message");
  const form = document.querySelector("form");
  const weightInput = document.getElementById("weight-input");
  const priceInput = document.getElementById("price-input");
  const quantityInput = document.getElementById("quantity-input");
  const addCartButton = document.getElementById("add-cart-button");

  // FUNCTIONS

  function setCategory(category) {
    categoryInput.value = category;
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
    console.log(categoryInput);
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
    addCartButton.classList.remove("hidden");
    priceInput.classList.add("hidden");
    quantityInput.classList.add("hidden");
    weightInput.classList.add("hidden");

    if (category === "1" || category === "2") {
      weightInput.classList.remove("hidden");
    } else if (category === "3") {
      weightInput.classList.remove("hidden");
      priceInput.classList.remove("hidden");
    } else if (category === "4") {
      quantityInput.classList.remove("hidden");
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
