// =======================
// API REQUESTS (AJAX)
// =======================

async function registerForSession(sessionId) {
  try {
    const response = await fetch(`/mon-compte/benevolat/sessions/register/${sessionId}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
    });
    const data = await response.json();
    return data.success ? data : null;
  } catch {
    return null;
  }
}

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

// =======================
// UTILITARY FUNCTIONS
// =======================

function bindCloseButton(modalSelector, buttonSelector) {
  const modal = document.querySelector(modalSelector);
  const closeButton = document.querySelector(buttonSelector);
  if (modal && closeButton) {
    closeButton.onclick = () => {
      const toggleBtn = modal.querySelector(".toggleRegistrationBtn");
      if (toggleBtn) toggleBtn.remove();
      modal.close();
    };
  }
}

// =======================
// CALENDAR INSTANCE & CONFIGURATION
// =======================
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    locale: "fr",
    height: "auto",
    headerToolbar: {
      left: "prev,next,today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    buttonText: {
      today: "Aujourd'hui",
      month: "Mois",
      week: "Semaine",
      day: "Jour",
    },
    events: "/mon-compte/benevolat/sessions",

    // =======================
    // AFFICHER L'EVENT ET SES INFOS AU CLIC
    // =======================
    eventClick: function (info) {
      const summaryModal = document.querySelector(".eventSummaryModal");

      let toggleRegistrationBtn = summaryModal.querySelector(".toggleRegistrationBtn");
      if (!toggleRegistrationBtn) {
        toggleRegistrationBtn = document.createElement("button");
        toggleRegistrationBtn.className = "toggleRegistrationBtn button-primary";
        summaryModal.querySelector(".eventModal-content").appendChild(toggleRegistrationBtn);
      }
      toggleRegistrationBtn.setAttribute("data-session-id", info.event.id);

      // Récupération des éléments de la modale
      const requiredVolunteers = info.event.extendedProps.requiredVolunteers;
      const modalTitle = summaryModal.querySelector(".modalTitle");
      const registeredVolunteers = summaryModal.querySelector(".modalRegisteredVolunteers");
      const volunteerList = summaryModal.querySelector(".modalVolunteerList");
      const modalTime = summaryModal.querySelector(".modalTime");
      const modalDescription = summaryModal.querySelector(".modalDescription");
      const modalLocation = summaryModal.querySelector(".modalLocation");
      let volunteerNames = info.event.extendedProps.volunteerFirstNames;
      let volunteerIds = info.event.extendedProps.volunteerIds;

      const currentUserId = parseInt(calendarEl.getAttribute("data-user-id"));
      const currentUserFirstName = calendarEl.getAttribute("data-user-firstname");

      modalTitle.textContent = info.event.title;
      modalDescription.innerHTML = `<i class="ph-fill ph-note"></i>${
        info.event.extendedProps.description || "Aucune description disponible."
      }`;
      modalLocation.innerHTML = `<i class="ph-fill ph-map-pin"></i>${
        info.event.extendedProps.location || "Lieu non spécifié."
      }`;

      // Affichage de la date et heure
      const start = info.event.start;
      const end = info.event.end;
      const optionsDate = { weekday: "short", day: "2-digit", month: "2-digit", year: "numeric" };
      const optionsTime = { hour: "2-digit", minute: "2-digit", hour12: false };
      const dateStr = start.toLocaleDateString("fr-FR", optionsDate);
      const startTime = start.toLocaleTimeString("fr-FR", optionsTime);
      const endTime = end ? end.toLocaleTimeString("fr-FR", optionsTime) : "";
      modalTime.innerHTML = `<i class="ph ph-clock"></i>${dateStr} ${startTime} - ${endTime}`;

      // =======================
      // GESTION INSCRIPTION / DÉSINSCRIPTION
      // =======================
      function updateButtonState(button, isRegistered) {
        button.className = isRegistered ? "toggleRegistrationBtn btn-danger" : "toggleRegistrationBtn button-primary";
        button.textContent = isRegistered ? "Se désinscrire" : "S'inscrire";
      }

      function updateVolunteerDisplay() {
        registeredVolunteers.innerHTML = `<i class="ph-fill ph-users-three"></i>
          Bénévoles inscrits : ${volunteerIds.length} / ${requiredVolunteers}`;
        volunteerList.innerHTML = "";
        volunteerNames.forEach((name) => {
          const li = document.createElement("li");
          li.textContent = name;
          volunteerList.appendChild(li);
        });
        updateButtonState(toggleRegistrationBtn, isUserRegistered);
      }

      let isUserRegistered = volunteerIds.includes(currentUserId);
      updateButtonState(toggleRegistrationBtn, isUserRegistered);

      toggleRegistrationBtn.onclick = async () => {
        if (isUserRegistered) {
          const result = await unsubscribeFromSession(info.event.id);
          if (result?.success) {
            isUserRegistered = false;
            volunteerIds = volunteerIds.filter((id) => id !== currentUserId);
            volunteerNames = volunteerNames.filter((name) => name !== currentUserFirstName);
          }
        } else {
          const result = await registerForSession(info.event.id);
          if (result?.success) {
            isUserRegistered = true;
            if (!volunteerIds.includes(currentUserId)) volunteerIds.push(currentUserId);
            if (!volunteerNames.includes(currentUserFirstName)) volunteerNames.push(currentUserFirstName);
          }
        }
        updateVolunteerDisplay();
      };

      updateVolunteerDisplay();
      summaryModal.showModal();
    },
  });

  calendar.render();

  // ===========================
  // GESTION DE LA FERMETURE DES MODALES
  // ===========================
  bindCloseButton(".eventSummaryModal", ".eventSummaryModal .closeButton");
});
