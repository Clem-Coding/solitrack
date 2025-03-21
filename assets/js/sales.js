import { formatNumber, formatInputValue, formatNumberFromString } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // 🟡 VARIABLES
  // ==========================
  const buttons = document.querySelectorAll("#sales-section .category-button");
  const categoryInput = document.getElementById("sales_item_categoryId");
  // const categoryErrorMessage = document.getElementById("error-message");
  const form = document.querySelector("form");
  const cartContainer = document.querySelector(".cart-container");
  const savedCart = JSON.parse(localStorage.getItem("cart"));
  const clearCartButton = document.querySelector(".clear-cart-button");
  const checkoutButton = document.querySelector(".checkout-button");

  const cartStatus = document.querySelector(".cart-status");
  const addCartButton = document.getElementById("add-cart-button");
  const salesForm = document.querySelector(".sales-form");

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

  salesForm.classList.remove("card");

  // let totalAmount = 0;

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
          updateCartDisplay(data.cart, data.total);
          // updateTotalDisplay(data.total);
          localStorage.setItem("totalAmount", data.total);
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
          // console.log("le data total quand je remove un item", data.total);

          // } else {
          //   updateTotalDisplay(data.total);
          // }

          localStorage.setItem("cart", JSON.stringify(data.cart));

          localStorage.setItem("totalAmount", JSON.stringify(data.total));

          updateCartDisplay(data.cart, data.total);
          if (data.cart.length === 0) {
            handleEmptyCart();
          }
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
          // cartStatus.innerHTML = "Votre panier est vide";
          handleEmptyCart();

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

  function handleEmptyCart() {
    cartStatus.innerHTML = "Votre panier est vide.";
    clearCartButton.classList.remove("show");
    clearCartButton.classList.add("hidden");
    checkoutButton.classList.add("hidden");
  }

  function handleNonEmptyCart() {
    clearCartButton.classList.add("show");
    clearCartButton.classList.remove("hidden");
    console.log(document.querySelector(".clear-cart-button").classList);

    checkoutButton.classList.remove("hidden");
  }

  function setCategory(category) {
    categoryInput.value = category;
  }

  function resetButtonStates(buttons) {
    buttons.forEach((button) => {
      button.classList.remove("active");
      button.setAttribute("aria-selected", "false");
    });
  }

  function resetInputs() {
    for (const key in inputs) {
      // inputs[key].value = "";
      inputs[key].removeAttribute("required");
      inputWrappers[key].classList.add("hidden");
      inputWrappers[key].classList.remove("flex");
    }
    addCartButton.classList.remove("hidden");
  }

  function showInput(input, wrapper) {
    input.setAttribute("required", "true");
    salesForm.classList.add("show");
    salesForm.classList.add("card");
    wrapper.classList.remove("hidden");
    wrapper.classList.add("flex");
  }

  function updateQuantityDisplay(newQuantity) {
    quantityInput.value = newQuantity;
  }

  function updateCartDisplay(cart, total) {
    console.log("le total j'update", total);
    if (total > 0) {
      cartStatus.innerHTML = `Total : <span class="data-price">${formatNumber(total)} €</span>`;
    } else {
      cartStatus.innerHTML = "Votre panier est vide.";
    }
    // const cartStatus = document.getElementById("total-price");
    if (cart.length === 0) {
      handleEmptyCart;
    } else {
      handleNonEmptyCart();
    }
    inputs.weight.value = "";
    inputs.price.value = "";
    inputs.quantity.value = 1;

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
        weightElement.innerHTML = `${item.weight}kg`;
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
      deleteButton.setAttribute("data-id", uniqueId);
      deleteButton.addEventListener("click", () => removeItemFromCart(uniqueId));
      articleElement.appendChild(deleteButton);
      itemElement.appendChild(articleElement);
      cartContainer.appendChild(itemElement);
    });
  }

  // function updateTotalDisplay(total) {
  //   if (cartStatus) {
  //     console.log(formatNumber(total));
  //     cartStatus.innerHTML = `Total : <span class="data-price">${formatNumber(total)} €</span>`;
  //   }
  // }

  // ==========================
  // 🔧 HANDLE FILTER CHANGES
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

    // if (!categoryInput.value) {
    //   categoryErrorMessage.style.display = "block";
    //   return;
    // }

    // categoryErrorMessage.style.display = "none";

    const errorflashMessage = document.querySelector(".flash-error");
    const succesflashMessage = document.querySelector(".flash-success");

    if (errorflashMessage) {
      errorflashMessage.remove();
    }

    if (succesflashMessage) {
      succesflashMessage.remove();
    }

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
    cartStatus.innerHTML = "Votre panier est vide.";
  }

  clearCartButton.addEventListener("click", () => clearCart());
});
