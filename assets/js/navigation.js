document.addEventListener("DOMContentLoaded", function (e) {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const menuIcon = document.querySelector("#menuIcon");
  const toggleButton = document.querySelector(".navbar-toggle");
  const navbar = document.querySelector(".navbar");
  const dropdown = document.querySelector(".dropdown");

  if (navbar) {
    navbar.classList.add("zindex-hidden");
  }

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================

  function toggleIconClass(icon, class1, class2) {
    if (icon.classList.contains(class1)) {
      icon.classList.replace(class1, class2);
    } else {
      icon.classList.replace(class2, class1);
    }
  }

  function toggleIconClassOnHover(icon, className) {
    icon.addEventListener("mouseenter", () => {
      icon.classList.add(className);
    });

    icon.addEventListener("mouseleave", () => {
      icon.classList.remove(className);
    });
  }

  // ==========================
  // MAIN NAV
  // ==========================
  if (toggleButton) {
    toggleButton.addEventListener("click", () => {
      navbar.classList.toggle("visible");
      toggleIconClass(menuIcon, "ph-list", "ph-x");
    });
  }

  //Ferme le menu principal si je clique n'importe où
  if (navbar) {
    document.addEventListener("click", function (e) {
      //Vérifie que je ne clique ni sur la navbar ni sur le button toggle
      if (!navbar.contains(e.target) && !toggleButton.contains(e.target)) {
        if (navbar.classList.contains("visible")) {
          navbar.classList.remove("visible");
          toggleIconClass(menuIcon, "ph-x", "ph-list");
        }
      }
    });
  }

  // ==========================
  // DROPDOWN MENU
  // ==========================

  // Ouvre le sous menu au clic sur l'encoche et rotate l'encoche
  if (dropdown) {
    const submenu = dropdown.querySelector(".submenu");
    const caretIcon = dropdown.querySelector(".caret-icon");
    const toggleDropdownButton = dropdown.querySelector("button");

    toggleDropdownButton.addEventListener("click", function (e) {
      submenu.classList.toggle("visible");
      caretIcon.classList.toggle("rotate");
    });

    // Ferme le sous-menu si je clique n'importe où
    document.addEventListener("click", function (e) {
      if (!dropdown.contains(e.target)) {
        submenu.classList.remove("visible");
        caretIcon.classList.remove("rotate");
      }
    });
  }

  // ==========================
  // USER MENU NAV
  // ==========================

  const userIcon = document.querySelector("#user-icon");
  const userMenu = document.querySelector(".user-menu");

  function toggleUserMenu() {
    userMenu.style.display = userMenu.style.display === "block" ? "none" : "block";
  }

  if (userIcon) {
    toggleIconClassOnHover(userIcon, "ph-duotone");
    userIcon.addEventListener("click", toggleUserMenu);

    document.addEventListener("click", (e) => {
      if (userMenu) {
        if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
          userMenu.style.display = "none";
        }
      }
    });
  }

  // ==========================
  // ICONS FOOTER
  // ==========================
  const footerIcons = document.querySelectorAll(".icons-container i");
  const footerLinks = document.querySelectorAll(".icons-container a");

  footerLinks.forEach((_, index) => {
    toggleIconClassOnHover(footerIcons[index], "ph-duotone");
  });
});
