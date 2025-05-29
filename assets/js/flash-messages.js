document.addEventListener("DOMContentLoaded", function () {
  const flashMessages = document.querySelectorAll(".flash");

  flashMessages.forEach((flashMessage) => {
    setTimeout(() => {
      flashMessage.classList.add("fade-out");
    }, 3000);

    setTimeout(() => {
      flashMessage.remove();
    }, 4000);
  });
});
