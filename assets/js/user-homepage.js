document.addEventListener("DOMContentLoaded", function () {
  const inWeight = parseFloat(document.getElementById("inWeight").dataset.value);
  const outWeight = parseFloat(document.getElementById("outWeight").dataset.value);
  const ctx = document.getElementById("myPieChart").getContext("2d");

  const myPieChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: ["Entrées", "Sorties"],
      datasets: [
        {
          label: "Poids",
          data: [inWeight, outWeight],
          backgroundColor: ["#EB5A47", " #080222"],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const rawValue = context.raw;
              return `${rawValue} kg`;
            },
          },
        },
      },
    },
  });

  // ===========================
  // ANNULER L'INSCRIPTION À UNE SESSION
  // ===========================

  async function unsubscribeFromSession(sessionId) {
    try {
      const response = await fetch(`/mon-compte/benevolat/sessions/unsubscribe/${sessionId}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
      });
      const data = await response.json();
      return data.success ? data : null;
    } catch {
      return null;
    }
  }

  const unsubscribeButtons = document.querySelectorAll(".unsubscribe-button");
  const unsubscribeModal = document.getElementById("confirm-modal");

  const btnCancel = unsubscribeModal.querySelector("#modal-cancel");
  const btnConfirm = unsubscribeModal.querySelector("#modal-confirm");

  btnCancel.addEventListener("click", () => {
    unsubscribeModal.close();
  });

  unsubscribeButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      const sessionId = button.dataset.sessionId;
      unsubscribeModal.dataset.sessionId = sessionId;
      unsubscribeModal.showModal();
    });
  });

  btnConfirm.addEventListener("click", async () => {
    const sessionId = unsubscribeModal.dataset.sessionId;
    const result = await unsubscribeFromSession(sessionId);
    if (result?.success) {
      window.location.reload();
    } else {
      alert("Une erreur est survenue lors de la désinscription. Veuillez réessayer.");
    }
  });
});
