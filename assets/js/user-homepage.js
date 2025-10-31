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

  const unsubscribeButtons = document.querySelectorAll(".button-destructive");
  unsubscribeButtons.forEach((button) => {
    button.addEventListener("click", async (event) => {
      console.log("Clicked unsubscribe button");
      event.preventDefault();
      const sessionId = button.getAttribute("data-session-id");
      const result = await unsubscribeFromSession(sessionId);
      if (result?.success) {
        //       // Recharger la page pour refléter les changements
        window.location.reload();
      } else {
        alert("Une erreur est survenue lors de la désinscription. Veuillez réessayer.");
      }
    });
  });
});
