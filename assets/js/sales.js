import { formatInputValue, formatNumberFromString } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categoryInput = document.getElementById("sales_item_categoryId");
  const errorMessage = document.getElementById("error-message");
  const form = document.querySelector("form");
  const cartContainer = document.getElementById("cart-container");
  const savedCart = JSON.parse(localStorage.getItem("cart"));
  const clearCartButton = document.querySelector(".clear-cart-button");
  const totalElement = document.querySelector("#total-price");

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

  // ==========================
  // 🟢 FETCH API
  // ==========================
  function addItemToCart(formData) {
    fetch(form.action, {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          updateCartDisplay(data.cart);
          updateTotalDisplay(data.total);
          localStorage.setItem("cart", JSON.stringify(data.cart));
        } else {
          console.error("Échec de l'ajout au panier:", data.message);
        }
      })
      .catch(async (error) => {
        if (error instanceof Response) {
          const text = await error.text();
        } else {
          console.error("Erreur non liée à une réponse HTTP");
        }
      });
  }

  function removeItemFromCart(uniqueId) {
    fetch("/cart/remove-item", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: uniqueId }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          updateCartDisplay(data.cart);
          updateTotalDisplay(data.total);
          localStorage.setItem("cart", JSON.stringify(data.cart));
        } else {
          console.error("Échec de la suppression de l'article:", data.message);
        }
      })
      .catch((error) => {
        console.error("Erreur lors de la suppression de l'article:", error);
      });
  }

  function clearCart() {
    fetch("/cart/clear", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          localStorage.removeItem("cart");
          cartContainer.innerHTML = "";
        } else {
          console.error(data.message);
        }
      })
      .catch((error) => {
        console.error("Erreur lors de la suppression du panier:", error);
      });
  }

  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================
  function setCategory(category) {
    categoryInput.value = category;
  }

  // function resetButtonColors() {
  //   buttons.forEach((button) => (button.style.backgroundColor = ""));
  // }

  function resetButtonStates(buttons) {
    buttons.forEach((button) => {
      button.classList.remove("active");
      button.setAttribute("aria-selected", "false");
    });
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

  function updateCartDisplay(cart) {
    cartContainer.innerHTML = "";

    cart.forEach((item) => {
      const uniqueId = item.uuid;
      const itemElement = document.createElement("li");
      itemElement.setAttribute("role", "listitem");

      const articleElement = document.createElement("article");
      const categoryElement = document.createElement("h3");
      categoryElement.textContent = item.category;
      articleElement.appendChild(categoryElement);

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
      deleteButton.addEventListener("click", () => removeItemFromCart(uniqueId));
      articleElement.appendChild(deleteButton);
      itemElement.appendChild(articleElement);
      cartContainer.appendChild(itemElement);
    });
  }

  function updateTotalDisplay(total) {
    if (totalElement) {
      totalElement.textContent = total;
    }
  }

  // ==========================
  // 🔧 HANDLE FILTER CHANGES
  // ==========================

  function handleButtonClick(event) {
    const clickedButton = event.target;
    setCategory(clickedButton.getAttribute("data-category"));
    // resetButtonColors();
    // clickedButton.style.backgroundColor = "#FFA500";
    resetButtonStates(buttons);
    clickedButton.classList.add("active");
    clickedButton.setAttribute("aria-selected", "true");
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

  function handleFormSubmit(event) {
    event.preventDefault();

    if (!categoryInput.value) {
      errorMessage.style.display = "block";
      return;
    }

    errorMessage.style.display = "none";

    const formData = new FormData(form);
    addItemToCart(formData);
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================
  buttons.forEach((button) => button.addEventListener("click", handleButtonClick));
  form.addEventListener("submit", handleFormSubmit);

  Object.values(inputs).forEach((input) => {
    input.addEventListener("input", () => {
      formatInputValue(input);
    });
  });

  if (savedCart && Array.isArray(savedCart)) {
    updateCartDisplay(savedCart);
  }

  clearCartButton.addEventListener("click", () => clearCart());
});
