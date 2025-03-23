document.addEventListener("DOMContentLoaded", function () {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const menuIcon = document.querySelector("#menuIcon");
  const toggleButton = document.querySelector(".navbar-toggle");
  const mainContainer = document.querySelector("main.container");
  console.log(mainContainer);

  const navbar = document.querySelector(".navbar");
  const dropdown = document.querySelector(".dropdown");
  const submenu = dropdown.querySelector(".submenu");
  const caretIcon = dropdown.querySelector("i");

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
    // Si on clique sur le lien "Tableau de bord" (l'élément li contenant ce lien), on ne veut pas qu'il recharge la page
    if (e.target.tagName === "A" && !e.target.closest(".submenu")) {
      e.preventDefault(); // Empêche la redirection pour ce lien particulier
      submenu.classList.toggle("visible"); // Toggles le sous-menu
      toggleIconClass(caretIcon, "ph-caret-down", "ph-caret-up"); // Change l'icône
    } else if (e.target.tagName !== "A") {
      // Si ce n'est pas un lien, toggle le sous-menu
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
  });
});
