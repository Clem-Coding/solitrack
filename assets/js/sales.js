import { formatNumber, formatInputValue, formatNumberFromString } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categorySelect = document.getElementById("sales_item_category");
  const form = document.querySelector(".sales-form");
  const cartContainer = document.querySelector(".cart-container");
  const savedCart = JSON.parse(localStorage.getItem("cart"));
  const clearCartButton = document.querySelector(".clear-cart-button");
  const checkoutButton = document.querySelector(".checkout-button");
  const cartSection = document.querySelector("#cart-section");
  const header = document.querySelector("header");
  const headerHeight = header.offsetHeight;

  const cartStatus = document.querySelector(".cart-status");
  const addCartButton = document.getElementById("add-cart-button");
  const addItemsCard = document.querySelector(".card.sales-card");

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

  const quantityWrapper = document.getElementById("quantity-input");
  const decreaseButton = quantityWrapper.querySelector(".quantity-decrease");
  const increaseButton = quantityWrapper.querySelector(".quantity-increase");

  const quantityInput = inputs.quantity;
  let quantity = Number(quantityInput.value);

  cartStatus.classList.add("text-center");
  addItemsCard.classList.remove("card");

  // ==========================
  // 🟢 FETCH
  // ==========================
  async function addItemToCart(formData) {
    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.status === "success") {
        scrollIntoViewAdjusted(cartSection, headerHeight);
        updateCartDisplay(data.cart, data.total);
        localStorage.setItem("totalAmount", data.total);
        localStorage.setItem("cart", JSON.stringify(data.cart));
      } else {
        showFormErrors(data.errors);
        console.error("Error", data.message);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  async function removeItemFromCart(uniqueId) {
    try {
      const response = await fetch("/cart/remove-item", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: uniqueId }),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.status === "success") {
        localStorage.setItem("cart", JSON.stringify(data.cart));
        localStorage.setItem("totalAmount", JSON.stringify(data.total));
        updateCartDisplay(data.cart, data.total);

        if (data.cart.length === 0) {
          handleEmptyCart();
        }
      } else {
        console.error("Failed to remove item:", data.message);
      }
    } catch (error) {
      console.error("Error removing item:", error);
    }
  }

  async function clearCart() {
    try {
      const response = await fetch("/cart/clear", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      });

      const data = await response.json();

      if (data.status === "success") {
        handleEmptyCart();
        localStorage.removeItem("cart");
        cartContainer.innerHTML = "";
      } else {
        console.error(data.message);
      }
    } catch (error) {
      console.error("Error while clearing the cart:", error);
    }
  }
  // ==========================
  // 🔍 UTILITY FUNCTIONS
  // ==========================

  function showFormErrors(errors) {
    for (const [fieldName, messages] of Object.entries(errors)) {
      const field = form.querySelector(`[name="sales_item[${fieldName}]"]`);
      if (field) {
        const errorDiv = document.createElement("p");
        errorDiv.classList.add("flash-error");
        errorDiv.setAttribute("role", "note");
        errorDiv.textContent = messages.join(", ");
        form.appendChild(errorDiv);
      }
    }
  }

  function scrollIntoViewAdjusted(elem, offset = 0) {
    window.scrollBy({
      top: elem.getBoundingClientRect().top - offset,
      behavior: "smooth",
    });
  }

  function handleEmptyCart() {
    cartStatus.classList.add("text-center");
    cartStatus.textContent = "Votre panier est vide.";
    clearCartButton.classList.remove("show");
    clearCartButton.classList.add("hidden");
    checkoutButton.classList.add("hidden");
  }

  function handleNonEmptyCart() {
    clearCartButton.classList.add("show");
    clearCartButton.classList.remove("hidden");
    checkoutButton.classList.remove("hidden");
  }

  function setCategory(category) {
    categorySelect.value = category;
  }

  function resetButtonStates(buttons) {
    buttons.forEach((button) => {
      button.classList.remove("active");
      button.setAttribute("aria-selected", "false");
    });
  }

  function resetInputs() {
    for (const key in inputs) {
      inputs[key].removeAttribute("required");
      inputWrappers[key].classList.add("hidden");
      inputWrappers[key].classList.remove("flex");
    }
    addCartButton.classList.remove("hidden");
  }

  function showInput(input, wrapper) {
    input.setAttribute("required", "true");
    addItemsCard.classList.add("show");
    addItemsCard.classList.add("card");
    wrapper.classList.remove("hidden");
    wrapper.classList.add("flex");
  }

  function updateQuantityDisplay(newQuantity) {
    quantityInput.value = newQuantity;
  }

  function updateCartDisplay(cart, total) {
    if (total > 0) {
      cartStatus.classList.remove("text-center");
      cartStatus.innerHTML = `Total : <span class="data-price">${formatNumber(total)} €</span>`;
    } else {
      cartStatus.classList.add("text-center");
      cartStatus.textContent = "Votre panier est vide.";
    }

    if (cart.length === 0) {
      handleEmptyCart;
    } else {
      handleNonEmptyCart();
    }

    const category = categorySelect.value;

    inputs.weight.value = "";
    inputs.price.value = "";
    inputs.quantity.value = category === "4" ? 1 : "";

    cartContainer.innerHTML = "";

    cart.forEach((item) => {
      const uniqueId = item.uuid;
      const itemElement = document.createElement("li");
      itemElement.setAttribute("role", "listitem");

      const articleElement = document.createElement("article");
      const categoryElement = document.createElement("h3");
      categoryElement.textContent = item.category;
      articleElement.appendChild(categoryElement);

      const detailsWrapper = document.createElement("div");
      detailsWrapper.classList.add("details-wrapper");

      if (item.quantity !== null) {
        const quantityElement = document.createElement("p");

        quantityElement.innerHTML = `Quantité : <span>${item.quantity}</span>`;
        detailsWrapper.appendChild(quantityElement);
      }

      if (item.weight !== null) {
        const weightElement = document.createElement("p");
        weightElement.innerHTML = `${formatNumberFromString(item.weight)}kg`;
        detailsWrapper.appendChild(weightElement);
      }

      if (detailsWrapper.childNodes.length > 0) {
        articleElement.appendChild(detailsWrapper);
      }

      if (item.price !== null) {
        const formattedPrice = formatNumberFromString(item.price);
        const priceElement = document.createElement("p");
        priceElement.innerHTML = `${formattedPrice}€`;
        detailsWrapper.appendChild(priceElement);
      }

      const deleteButton = document.createElement("button");
      deleteButton.innerHTML = '<i class="ph ph-x-circle"></i>';
      deleteButton.classList.add("btn-cross-delete");
      deleteButton.setAttribute("data-id", uniqueId);
      deleteButton.addEventListener("click", () => removeItemFromCart(uniqueId));
      articleElement.appendChild(deleteButton);
      itemElement.appendChild(articleElement);
      cartContainer.appendChild(itemElement);
    });
  }

  // ==========================
  // 🔧 HANDLE FUNCTIONS
  // ==========================

  function handleButtonClick(event) {
    const clickedButton = event.target;
    setCategory(clickedButton.getAttribute("data-category"));

    resetButtonStates(buttons);
    clickedButton.classList.add("active");
    clickedButton.setAttribute("aria-selected", "true");
    handleCategoryChange();
  }

  function handleCategoryChange() {
    const category = categorySelect.value;
    resetInputs();

    switch (category) {
      case "1":
      case "2":
        showInput(inputs.weight, inputWrappers.weight);
        inputs.quantity.value = "";
        break;
      case "3":
        showInput(inputs.weight, inputWrappers.weight);
        showInput(inputs.price, inputWrappers.price);
        inputs.quantity.value = "";
        break;
      case "4":
        showInput(inputs.quantity, inputWrappers.quantity);
        inputs.quantity.value = 1;
        break;
      case "5":
        showInput(inputs.quantity, inputWrappers.quantity);
        showInput(inputs.weight, inputWrappers.weight);
        inputs.quantity.value = 1;
        break;
    }
  }

  function handleFormSubmit(event) {
    event.preventDefault();

    const errorflashMessage = document.querySelector(".flash-error");
    const succesflashMessage = document.querySelector(".flash-success");

    if (errorflashMessage) {
      errorflashMessage.remove();
    }

    if (succesflashMessage) {
      succesflashMessage.remove();
    }
    // FormData is a JavaScript object that gathers all the form's input values and files, making it easy to send them in a request.
    const formData = new FormData(form);
    addItemToCart(formData);
  }

  // ==========================
  // 🖱️ EVENT LISTENERS
  // ==========================

  buttons.forEach((button) => button.addEventListener("click", handleButtonClick));

  form.addEventListener("submit", handleFormSubmit);

  decreaseButton.addEventListener("click", () => {
    if (quantity > 1) {
      quantity--;
      updateQuantityDisplay(quantity);
    }
  });

  increaseButton.addEventListener("click", () => {
    quantity++;
    updateQuantityDisplay(quantity);
  });

  Object.values(inputs).forEach((input) => {
    input.addEventListener("input", () => {
      formatInputValue(input);
    });
  });

  if (savedCart && Array.isArray(savedCart)) {
    let totalAmount = Number(localStorage.getItem("totalAmount"));
    updateCartDisplay(savedCart, totalAmount);
  } else {
    cartStatus.classList.add("text-center");
    cartStatus.textContent = "Votre panier est vide.";
  }

  clearCartButton.addEventListener("click", () => clearCart());
});
