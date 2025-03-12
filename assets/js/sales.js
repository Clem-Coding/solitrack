import { formatInputValue, formatNumberFromString } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  //CONSTANTS
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categoryInput = document.getElementById("sales_item_categoryId");
  const errorMessage = document.getElementById("error-message");
  const form = document.querySelector("form");
  const cartContainer = document.getElementById("cart-container");

  const inputWrappers = {
    weight: document.getElementById("weight-input"),
    price: document.getElementById("price-input"),
    quantity: document.getElementById("quantity-input"),
  };

  const inputs = {
    weight: inputWrappers.weight.querySelector("input"),
    price: inputWrappers.price.querySelector("input"),
    quantity: inputWrappers.quantity.querySelector("input"),
  };

  const addCartButton = document.getElementById("add-cart-button");

  //FUNCTIONS

  function setCategory(category) {
    categoryInput.value = category;
  }

  function resetButtonColors() {
    buttons.forEach((button) => (button.style.backgroundColor = ""));
  }

  function handleButtonClick(event) {
    const clickedButton = event.target;
    setCategory(clickedButton.getAttribute("data-category"));
    resetButtonColors();
    clickedButton.style.backgroundColor = "#FFA500";
    handleCategoryChange();
  }

  function handleCategoryChange() {
    const category = categoryInput.value;
    resetInputs();

    switch (category) {
      case "1":
      case "2":
        showInput(inputs.weight, inputWrappers.weight);
        break;
      case "3":
        showInput(inputs.weight, inputWrappers.weight);
        showInput(inputs.price, inputWrappers.price);
        break;
      case "4":
        showInput(inputs.quantity, inputWrappers.quantity);
        break;
    }
  }

  function resetInputs() {
    for (const key in inputs) {
      inputs[key].value = "";
      inputs[key].removeAttribute("required");
      inputWrappers[key].classList.add("hidden");
    }
    addCartButton.classList.remove("hidden");
  }

  function showInput(input, wrapper) {
    input.setAttribute("required", "true");
    wrapper.classList.remove("hidden");
  }

  function handleFormSubmit(event) {
    event.preventDefault();

    if (!categoryInput.value) {
      errorMessage.style.display = "block";
      return;
    }

    errorMessage.style.display = "none";

    const formData = new FormData(form);
    console.log("Form Data:", formData);
    fetch(form.action, {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => {
        // console.log("Réponse brute du serveur:", response);

        return response.json();
      })
      .then((data) => {
        if (data.status === "success") {
          updateCartDisplay(data.cart);
          console.log("Article ajouté au panier:", data.cart);
        } else {
          console.error("Échec de l'ajout au panier:", data.message);
        }
      })
      .catch(async (error) => {
        // console.error("Erreur JS:", error);
        if (error instanceof Response) {
          const text = await error.text();
          console.error("Réponse brute du serveur:", text);
        } else {
          console.error("Erreur non liée à une réponse HTTP");
        }
      });
  }

  function updateCartDisplay(cart) {
    const cartContainer = document.getElementById("cart-container");
    cartContainer.innerHTML = ""; // ??

    cart.forEach((item) => {
      const uniqueId = `${item.category}-${item.weight}-${item.price}`; //c'est nul comme manière de faire
      console.log("l'item: ", uniqueId);
      const itemElement = document.createElement("li");
      itemElement.setAttribute("role", "listitem");

      const articleElement = document.createElement("article");

      const categoryElement = document.createElement("h3");
      categoryElement.textContent = item.category;
      articleElement.appendChild(categoryElement);

      //mettre des else if
      if (item.quantity !== null) {
        const quantityElement = document.createElement("p");
        quantityElement.innerHTML = `Quantité : <span>${item.quantity}</span>`;
        articleElement.appendChild(quantityElement);
      }

      if (item.weight !== null) {
        const weightElement = document.createElement("p");
        weightElement.innerHTML = `Poids : <span>${item.weight}kg</span>`;
        articleElement.appendChild(weightElement);
      }

      if (item.price !== null) {
        const formattedPrice = formatNumberFromString(item.price);
        const priceElement = document.createElement("p");
        priceElement.innerHTML = `Prix : <span>${formattedPrice}€</span>`;
        articleElement.appendChild(priceElement);
      }

      const deleteButton = document.createElement("button");
      deleteButton.textContent = "Supprimer";
      deleteButton.setAttribute("data-id", uniqueId);
      deleteButton.addEventListener("click", handleDeleteItem);
      articleElement.appendChild(deleteButton);
      itemElement.appendChild(articleElement);
      cartContainer.appendChild(itemElement);
    });
  }

  function handleDeleteItem(event) {
    const uniqueId = event.target.getAttribute("data-id");
    console.log("Index de l'article à supprimer:", uniqueId);

    fetch("/cart/remove-item", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ id: uniqueId }),
    })
      .then((response) => {
        console.log("Réponse brute:", response);
        return response.json();
      })

      .then((data) => {
        if (data.status === "success") {
          updateCartDisplay(data.cart);
        } else {
          console.error("Échec de la suppression de l'article:", data.message);
        }
      })
      .catch((error) => {
        console.error("Erreur lors de la suppression de l'article:", error);
      });
  }

  // EVENT LISTENERS

  buttons.forEach((button) => button.addEventListener("click", handleButtonClick));
  form.addEventListener("submit", handleFormSubmit);

  Object.values(inputs).forEach((input) => {
    input.addEventListener("input", () => {
      formatInputValue(input);
    });
  });
});
