document.addEventListener("DOMContentLoaded", (event) => {
  const buttons = document.querySelectorAll("button[data-category]");
  console.log(buttons);
  const categoryInput = document.getElementById("donation_form_categoryId");
  // categoryInput.value = "";
  const form = document.querySelector("form");
  const errorMessage = document.getElementById("error-message");
  console.log(errorMessage);

  buttons.forEach((button) => {
    button.addEventListener("click", (e) => {
      const clickedButton = e.target;
      setCategory(clickedButton.getAttribute("data-category"));
      resetButtonColors(buttons);
      clickedButton.style.backgroundColor = "#FFA500";
      resetButtonColors(buttons);
      clickedButton.style.backgroundColor = "#FFA500";
    });
  });

  function setCategory(category) {
    categoryInput.value = category;
    console.log("Catégorie sélectionnée :", categoryInput.value);
  }

  function resetButtonColors(buttons) {
    buttons.forEach((button) => {
      button.style.backgroundColor = "";
    });
  }

  form.addEventListener("submit", function (event) {
    if (!categoryInput.value) {
      event.preventDefault();
      errorMessage.style.display = "block";
    } else {
      errorMessage.style.display = "none";
    }
  });
});

//A faire plus tard avec des classes .active en css
// + set attribute aria-selected : false/true
