// document.addEventListener("DOMContentLoaded", function () {
//   const toggleButton = document.getElementById("toggleMenu");
//   const menuIcon = document.getElementById("menuIcon");
//   const navbarMenu = document.querySelector(".navbar");

//   let showLinks = false;

//   toggleButton.addEventListener("click", () => {
//     showLinks = !showLinks;

//     // Changer l'icône (menu ou croix)
//     if (showLinks) {
//       menuIcon.classList.remove("ph-list");
//       menuIcon.classList.add("ph-x");
//       navbarMenu.classList.add("show");
//     } else {
//       menuIcon.classList.remove("ph-x");
//       menuIcon.classList.add("ph-list");
//       navbarMenu.classList.remove("show");
//     }
//   });
// });

document.addEventListener("DOMContentLoaded", function () {
  console.log("coucou la page");

  const mainContainer = document.querySelector(".main-container");
  console.log(mainContainer);
  const toggleButton = document.querySelector(".navbar-toggle");
  const navbar = document.querySelector(".navbar");
  const menuIcon = document.querySelector("#menuIcon");

  toggleButton.addEventListener("click", () => {
    navbar.classList.toggle("visible");

    navbar.style.zIndex = "-1";
    mainContainer.style.zIndex = "-10";

    // Basculer l'icône entre "ph-list" et "ph-x"
    if (menuIcon.classList.contains("ph-list")) {
      menuIcon.classList.replace("ph-list", "ph-x");
    } else {
      menuIcon.classList.replace("ph-x", "ph-list");
    }
  });

  navbar.addEventListener("transitionend", () => {
    if (!navbar.classList.contains("visible")) {
      mainContainer.style.zIndex = "1";
      navbar.style.zIndex = "-1";
    } else {
      navbar.style.zIndex = "1";
      mainContainer.style.zIndex = "-10";
    }
  });
});

// menuIcon.classList.remove("ph-list");
//       menuIcon.classList.add("ph-x");
