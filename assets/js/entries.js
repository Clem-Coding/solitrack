document.addEventListener("DOMContentLoaded", () => {
  // CONSTANTS
  const buttons = document.querySelectorAll("#entry-section .category-button");
  const categoryInput = document.getElementById("donation_form_categoryId");
  const form = document.querySelector("form");
  const errorMessage = document.getElementById("error-message");

  // FUNCTIONS
  function setCategory(category) {
    categoryInput.value = category;
    console.log("Catégorie sélectionnée :", categoryInput.value);
  }

  function resetButtonColors(buttons) {
    buttons.forEach((button) => {
      button.style.backgroundColor = "";
    });
  }

  function handleButtonClick(e) {
    const clickedButton = e.target;
    setCategory(clickedButton.getAttribute("data-category"));
    resetButtonColors(buttons);
    clickedButton.style.backgroundColor = "#FFA500";
  }

  function handleFormSubmit(event) {
    if (!categoryInput.value) {
      event.preventDefault();
      errorMessage.style.display = "block";
    } else {
      errorMessage.style.display = "none";
    }
  }

  // EVENT LISTENERS
  buttons.forEach((button) => {
    button.addEventListener("click", handleButtonClick);
  });

  form.addEventListener("submit", handleFormSubmit);

  // LOGS
});

//A faire plus tard avec des classes .active en css
// + set attribute aria-selected : false/true
