import { formatInputValue } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const buttons = document.querySelectorAll(".category-button");
  const categoryInput = document.getElementById("donation_form_categoryId");
  const form = document.querySelector("form");
  const weightInput = document.getElementById("donation_form_weight");
  const errorMessage = document.getElementById("error-message");

  weightInput.value = "";

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================
  function setCategory(category) {
    categoryInput.value = category;
  }

  function resetButtonStates(buttons) {
    buttons.forEach((button) => {
      button.classList.remove("active");
      button.setAttribute("aria-selected", "false");
    });
  }

  function showError() {
    errorMessage.classList.remove("hide");
    errorMessage.classList.add("show");
  }

  function hideError() {
    errorMessage.classList.remove("show");
    errorMessage.classList.add("hide");
  }

  // ==========================
  // 🔧 HANDLE FILTER CHANGES
  // ==========================

  function handleButtonClick(event) {
    const clickedButton = event.currentTarget;

    if (!errorMessage.classList.contains("show")) {
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

      showError();
    } else {
      hideError();
    }
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

  buttons.forEach((button) => {
    button.addEventListener("click", (event) => {
      handleButtonClick(event);
      hideError();
    });
  });

  form.addEventListener("submit", handleFormSubmit);

  weightInput.addEventListener("input", () => {
    formatInputValue(weightInput);
  });
});
