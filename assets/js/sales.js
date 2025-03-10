document.addEventListener("DOMContentLoaded", (event) => {
  // CONSTANTES
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categoryInput = document.getElementById("sales_item_categoryId");
  const errorMessage = document.getElementById("error-message");
  const form = document.querySelector("form");
  const weightInputWrapper = document.getElementById("weight-input");
  const priceInputWrapper = document.getElementById("price-input");
  const quantityInputWrapper = document.getElementById("quantity-input");

  // Sélectionner les inputs
  const weightInput = weightInputWrapper.querySelector("input");
  const priceInput = priceInputWrapper.querySelector("input");
  const quantityInput = quantityInputWrapper.querySelector("input");

  const addCartButton = document.getElementById("add-cart-button");

  // Fonction pour définir la catégorie dans le champ caché
  function setCategory(category) {
    categoryInput.setAttribute("value", category);
  }

  // Fonction pour réinitialiser la couleur des boutons
  function resetButtonColors(buttons) {
    buttons.forEach((button) => {
      button.style.backgroundColor = "";
    });
  }

  // Fonction qui gère le clic sur les boutons des catégories
  function handleButtonClick(event) {
    const clickedButton = event.target;
    setCategory(clickedButton.getAttribute("data-category"));
    resetButtonColors(buttons);
    clickedButton.style.backgroundColor = "#FFA500";
  }

  // Fonction pour gérer la soumission du formulaire
  function handleFormSubmit(event) {
    // Vérifie si une catégorie a été sélectionnée
    if (!categoryInput.value) {
      event.preventDefault();
      errorMessage.style.display = "block";
    } else {
      errorMessage.style.display = "none";
    }
  }

  // Fonction pour gérer le changement de catégorie
  function handleCategoryChange() {
    const category = categoryInput.value;

    // Réinitialisation des valeurs des champs
    weightInput.value = "";
    priceInput.value = "";
    quantityInput.value = "";

    // Retirer l'attribut "required" de tous les champs par défaut
    weightInput.removeAttribute("required");
    priceInput.removeAttribute("required");
    quantityInput.removeAttribute("required");

    addCartButton.classList.remove("hidden");
    priceInputWrapper.classList.add("hidden");
    quantityInputWrapper.classList.add("hidden");
    weightInputWrapper.classList.add("hidden");

    // Catégorie 1 ou 2 : Afficher Poids
    if (category === "1" || category === "2") {
      weightInputWrapper.classList.remove("hidden");
      weightInput.setAttribute("required", "true");
    }
    // Catégorie 3 : Afficher Poids et Prix
    else if (category === "3") {
      weightInputWrapper.classList.remove("hidden");
      priceInputWrapper.classList.remove("hidden");
      weightInput.setAttribute("required", "true");
      priceInput.setAttribute("required", "true");
    } else if (category === "4") {
      quantityInputWrapper.classList.remove("hidden");
      quantityInput.setAttribute("required", "true");
    }
  }

  buttons.forEach((button) => {
    button.addEventListener("click", (event) => {
      handleButtonClick(event);
      handleCategoryChange();
    });
  });

  form.addEventListener("submit", handleFormSubmit);
});
