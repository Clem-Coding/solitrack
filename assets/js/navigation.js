document.addEventListener("DOMContentLoaded", function () {
  const navbarToggle = document.querySelector(".navbar-toggle");
  const navbarMenu = document.querySelector(".navbar-menu");

  navbarToggle.addEventListener("click", () => {
    navbarMenu.classList.toggle("active");
  });
});
