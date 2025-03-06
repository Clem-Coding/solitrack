document.addEventListener("DOMContentLoaded", (event) => {
  const buttons = document.querySelectorAll("button[data-category]");
  const categoryInput = document.getElementById("donation_form_categoryId");
  console.log("l'input category", categoryInput);

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

      //A faire plus tard avec des classes .active en css
    });
  }

  document
    .getElementById("deleteLastEntry")
    .addEventListener("click", function () {
      console.log("detele");
      fetch("/entrees/delete-last", {
        method: "DELETE",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById("lastEntryDetails").textContent =
              "Blablabla";
          } else {
            alert(data.message);
          }
        })
        .catch((error) => console.error("Error:", error));
    });
});
