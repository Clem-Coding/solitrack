document.addEventListener("DOMContentLoaded", function () {
  // console.log("coucou les flash");
  const flashMessages = document.querySelectorAll(".flash");

  flashMessages.forEach((flashMessage) => {
    setTimeout(() => {
      flashMessage.classList.add("fade-out");
    }, 2000);

    setTimeout(() => {
      flashMessage.remove();
    }, 3000);
  });
});
