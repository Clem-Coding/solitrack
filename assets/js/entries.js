import { formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
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

  function resetButtonStates(buttons) {
    buttons.forEach((button) => {
      button.classList.remove("active");
      button.setAttribute("aria-selected", "false");
    });
  }

  // ==========================
  // 🔧 HANDLE FILTER CHANGES
  // ==========================

  function handleButtonClick(e) {
    const clickedButton = e.currentTarget;
    console.log("je clique");

    if (errorMessage.style.display !== "block") {
      weightInput.value = "";
    }

    setCategory(clickedButton.getAttribute("data-category"));
    resetButtonStates(buttons);

    clickedButton.classList.add("active");
    clickedButton.setAttribute("aria-selected", "true");
  }

  function handleFormSubmit(event) {
    if (!categoryInput.value) {
      event.preventDefault();
      errorMessage.style.display = "block";
    } else {
      errorMessage.style.display = "none";
    }
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

  buttons.forEach((button) => {
    button.addEventListener("click", handleButtonClick);
  });

  form.addEventListener("submit", handleFormSubmit);

  weightInput.addEventListener("input", () => {
    formatInputValue(weightInput);
  });
});
