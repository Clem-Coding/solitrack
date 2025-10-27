import { formatInputValue } from "./helpers/utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  //  TOGGLE PASSWORD VISIBILITY
  // ==========================
  const eyeIcons = document.querySelectorAll(".toggle-password");

  eyeIcons.forEach((icon) => {
    icon.addEventListener("click", function () {
      const passwordField = icon.previousElementSibling;
      if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("ph-eye");
        icon.classList.add("ph-eye-slash");
      } else {
        passwordField.type = "password";
        icon.classList.remove("ph-eye-slash");
        icon.classList.add("ph-eye");
      }
    });
  });

  // ==========================
  //  FORM ON DASHBOARD HOME PAGE
  // ==========================

  const outgoingWeighingInput = document.getElementById("outgoing_weighing_weight");

  if (outgoingWeighingInput) {
    outgoingWeighingInput.addEventListener("input", () => {
      formatInputValue(outgoingWeighingInput);
    });
  }
});
