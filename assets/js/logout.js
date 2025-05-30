document.addEventListener("DOMContentLoaded", () => {
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      localStorage.removeItem("cart");
    });
  }

  if (typeof window.PHP_SESSION_ACTIVE !== "undefined" && !window.PHP_SESSION_ACTIVE) {
    localStorage.removeItem("cart");
  }
});
