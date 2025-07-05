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

  //Close the main menu if I click anywhere
  if (navbar) {
    document.addEventListener("click", function (e) {
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

  //Open the submenu on click and rotate the caret icon
  if (dropdown) {
    const submenu = dropdown.querySelector(".submenu");
    const caretIcon = dropdown.querySelector(".caret-icon");
    const toggleDropdownButton = dropdown.querySelector("button");

    toggleDropdownButton.addEventListener("click", function (e) {
      submenu.classList.toggle("visible");
      caretIcon.classList.toggle("rotate");
    });

    // Close the submenu if I click anywhere outside of it
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

// ==========================
// USER ACCOUNT
// ==========================

document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab");
  const contents = document.querySelectorAll(".tab-content");

  // Activate the tab based on localStorage or default to "infos"
  const savedTab = localStorage.getItem("activeTab") || "infos";
  activateTab(savedTab);

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const target = tab.dataset.tab;
      localStorage.setItem("activeTab", target);
      activateTab(target);
    });
  });

  function activateTab(name) {
    tabs.forEach((t) => t.classList.remove("active"));
    document.querySelector(`.tab[data-tab="${name}"]`)?.classList.add("active");

    contents.forEach((content) => content.classList.add("hidden"));
    document.getElementById("tab-" + name)?.classList.remove("hidden");
  }
});
