document.addEventListener("DOMContentLoaded", () => {
  const eyeIcons = document.querySelectorAll(".toggle-password");

  eyeIcons.forEach((icon) => {
    icon.addEventListener("click", function () {
      console.log("je clique sur un oeil");
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
});
