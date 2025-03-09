document.addEventListener("DOMContentLoaded", (event) => {
  // CONSTANTS

  const salesSection = document.querySelector("#sales-section");
  console.log(salesSection);

  //   const buttons = document.querySelectorAll("#sales-section .category-button");
  //   if (salesSection) {
  const salesButtons = salesSection.querySelectorAll(".category-button");
  //   }

  //   console.log(buttons);

  // FUNCTIONS

  // EVENT LISTENERS

  //LOGS
  if (salesSection) {
    console.log("les boutons", salesButtons);
  }
});
