import { formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // CONSTANTS
  const buttons = document.querySelectorAll("#entry-section .category-button");
  const categoryInput = document.getElementById("donation_form_categoryId");
  const form = document.querySelector("form");
  const weightInput = document.getElementById("donation_form_weight");
  const errorMessage = document.getElementById("error-message");

  weightInput.value = "";

  // FUNCTIONS
  function setCategory(category) {
    categoryInput.value = category;
  }

  function resetButtonColors(buttons) {
    buttons.forEach((button) => {
      button.style.backgroundColor = "";
    });
  }

  function handleButtonClick(e) {
    const clickedButton = e.target;
    if (errorMessage.style.display !== "block") {
      weightInput.value = "";
    }

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

  // EVENT LISTENERS
  buttons.forEach((button) => {
    button.addEventListener("click", handleButtonClick);
  });

  form.addEventListener("submit", handleFormSubmit);

  weightInput.addEventListener("input", (event) => {
    formatInputValue(weightInput);
  });
  // LOGS
});

//A faire plus tard avec des classes .active en css
// + set attribute aria-selected : false/true
