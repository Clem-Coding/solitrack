document.addEventListener("DOMContentLoaded", function () {
  console.log("coucou la page");

  const mainContainer = document.querySelector(".main-container");
  console.log(mainContainer);
  const toggleButton = document.querySelector(".navbar-toggle");
  const navbar = document.querySelector(".navbar");
  const menuIcon = document.querySelector("#menuIcon");
  const dropdown = document.querySelector(".dropdown");
  const submenu = dropdown.querySelector(".submenu");
  const caretIcon = dropdown.querySelector("i");

  toggleButton.addEventListener("click", () => {
    navbar.classList.toggle("visible");
    navbar.classList.remove("zindex-visible");
    navbar.classList.add("zindex-hidden");
    mainContainer.classList.remove("zindex-visible");
    mainContainer.classList.add("zindex-hidden");

    if (menuIcon.classList.contains("ph-list")) {
      menuIcon.classList.replace("ph-list", "ph-x");
    } else {
      menuIcon.classList.replace("ph-x", "ph-list");
    }
  });

  navbar.addEventListener("transitionend", () => {
    if (!navbar.classList.contains("visible")) {
      navbar.classList.remove("zindex-visible");
      navbar.classList.add("zindex-hidden");

      mainContainer.classList.remove("zindex-hidden");
      mainContainer.classList.add("zindex-visible");
    } else {
      navbar.classList.remove("zindex-hidden");
      navbar.classList.add("zindex-visible");

      mainContainer.classList.remove("zindex-visible");
      mainContainer.classList.add("zindex-hidden");
    }
  });

  dropdown.addEventListener("click", function (e) {
    e.preventDefault();
    submenu.classList.toggle("visible");

    if (submenu.classList.contains("visible")) {
      caretIcon.classList.replace("ph-caret-down", "ph-caret-up");
    } else {
      caretIcon.classList.replace("ph-caret-up", "ph-caret-down");
    }
  });

  document.addEventListener("click", (e) => {
    console.log("je clique n'importe où");
    if (!navbar.contains(e.target) && !toggleButton.contains(e.target)) {
      navbar.classList.toggle("visible");
      navbar.classList.remove("zindex-visible");
      navbar.classList.add("zindex-hidden");
      navbar.classList.remove("visible");
      menuIcon.classList.replace("ph-x", "ph-list");
    }
  });
});
