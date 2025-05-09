import { formatInputValue } from "./utils.js";
import confetti from "canvas-confetti";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const buttons = document.querySelectorAll(".category-button");
  const categoryInput = document.getElementById("donation_form_categoryId");
  const form = document.querySelector(".donation-form");
  const weightInput = document.getElementById("donation_form_weight");
  const errorMessage = document.getElementById("error-message");
  const lastEntrySection = document.querySelector("#last-entry-section");
  const lastEntryInfo = document.querySelector(".last-entry-info");
  const feedbackMessageElement = document.querySelector(".feedback-message");
  const isRecordJustBeaten = feedbackMessageElement?.getAttribute("data-record-achieved") === "true";

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

  function updateEntryMessage(entryName, entryWeight) {
    if (entryName && entryWeight) {
      entryMessage.textContent = `${entryName} | ${entryWeight} kg`;
    } else {
      entryMessage.textContent = "Aucune entrée à supprimer";
    }
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
      // event.preventDefault();
      // lastEntrySection.scrollIntoView({ behavior: "smooth" });
      hideError();
    }
  }
  // ==========================
  // 🟡 CONFETTIS 🎉
  // ==========================
  let launchCount = 0;

  function launchConfetti() {
    if (isRecordJustBeaten && launchCount < 4) {
      launchCount++;
      confetti({
        particleCount: 200,
        spread: 90,
        origin: { x: 0.5, y: 0.5 },
        duration: 2000,
      });
    }
  }

  setInterval(launchConfetti, 1000);

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
