document.addEventListener("DOMContentLoaded", function () {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const menuIcon = document.querySelector("#menuIcon");
  const toggleButton = document.querySelector(".navbar-toggle");
  const mainContainer = document.querySelector("main.container");
  const navbar = document.querySelector(".navbar");
  const dropdown = document.querySelector(".dropdown");
  const submenu = dropdown.querySelector(".submenu");
  const caretIcon = dropdown.querySelector("i");

  // ==========================
  // 🎯 USER MENU VARIABLES
  // ==========================
  const userIcon = document.querySelector("#user-icon");
  const userMenu = document.querySelector(".user-menu");

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================
  function toggleVisibility(element, visibleClass, hiddenClass) {
    element.classList.toggle(visibleClass);
    element.classList.remove(hiddenClass);
    element.classList.add(hiddenClass);
  }

  function toggleZIndex(element, visibleClass, hiddenClass) {
    element.classList.remove(visibleClass);
    element.classList.add(hiddenClass);
  }

  function toggleIconClass(icon, class1, class2) {
    if (icon.classList.contains(class1)) {
      icon.classList.replace(class1, class2);
    } else {
      icon.classList.replace(class2, class1);
    }
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

  toggleButton.addEventListener("click", () => {
    toggleVisibility(navbar, "visible");
    toggleZIndex(navbar, "zindex-visible", "zindex-hidden");
    toggleZIndex(mainContainer, "zindex-visible", "zindex-hidden");
    toggleIconClass(menuIcon, "ph-list", "ph-x");
  });

  navbar.addEventListener("transitionend", () => {
    if (!navbar.classList.contains("visible")) {
      toggleZIndex(navbar, "zindex-visible", "zindex-hidden");
      toggleZIndex(mainContainer, "zindex-hidden", "zindex-visible");
    } else {
      toggleZIndex(navbar, "zindex-hidden", "zindex-visible");
      toggleZIndex(mainContainer, "zindex-visible", "zindex-hidden");
    }
  });

  dropdown.addEventListener("click", function (e) {
    if (e.target.tagName === "A" && !e.target.closest(".submenu")) {
      e.preventDefault();
      submenu.classList.toggle("visible");
      toggleIconClass(caretIcon, "ph-caret-down", "ph-caret-up");
    } else if (e.target.tagName !== "A") {
      e.preventDefault();
      submenu.classList.toggle("visible");
      toggleIconClass(caretIcon, "ph-caret-down", "ph-caret-up");
    }
  });
  document.addEventListener("click", (e) => {
    if (!navbar.contains(e.target) && !toggleButton.contains(e.target)) {
      if (navbar.classList.contains("visible")) {
        navbar.classList.remove("visible");
        toggleZIndex(navbar, "zindex-visible", "zindex-hidden");
        toggleIconClass(menuIcon, "ph-x", "ph-list");
      }
    }

    if (!dropdown.contains(e.target) && !submenu.contains(e.target)) {
      submenu.classList.remove("visible");
      toggleIconClass(caretIcon, "ph-caret-down", "ph-caret-up"); // Restaurer l'icône
    }
  });

  // ==========================
  // 🎯 USER MENU
  // ==========================

  function toggleUserMenu() {
    userMenu.style.display = userMenu.style.display === "block" ? "none" : "block";
  }

  if (userIcon) {
    userIcon.addEventListener("click", toggleUserMenu);
  }

  document.addEventListener("click", (e) => {
    if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
      userMenu.style.display = "none";
    }
  });
});
