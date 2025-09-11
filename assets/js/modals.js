document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("confirm-modal");
  const confirmBtn = document.getElementById("modal-confirm");
  const cancelBtn = document.getElementById("modal-cancel");
  let formToSubmit = null;

  function openModal(form) {
    formToSubmit = form;
    modal.style.display = "flex";
  }

  function closeModal() {
    modal.style.display = "none";
    formToSubmit = null;
  }

  document.querySelectorAll("#delete-user").forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      openModal(form);
    });
  });

  confirmBtn.addEventListener("click", () => {
    if (formToSubmit) {
      formToSubmit.submit();
    }
    closeModal();
  });

  cancelBtn.addEventListener("click", closeModal);
});
