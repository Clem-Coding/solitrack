import { AVAILABLE, UNAVAILABLE, ALERT_RED, USER_REGISTERED, PAST_EVENTS } from "./helpers/constants.js";

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
    closeButton.onclick = () => modal.close();
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.close();
      }
    });
  }
}

// =======================
// CALENDAR INSTANCE & CONFIGURATION
// =======================
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  const isMobile = window.innerWidth < 768;
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    locale: "fr",
    height: "auto",
    eventDisplay: "block",
    headerToolbar: {
      left: "prev,next,today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },

    views: {
      ...(isMobile && {
        timeGridWeek: {
          dayHeaderFormat: { weekday: "short", day: "numeric", omitCommas: true },
        },
      }),
    },

    buttonText: {
      today: "Aujourd'hui",
      month: "Mois",
      week: "Semaine",
      day: "Jour",
    },
    events: "/mon-compte/benevolat/sessions",

    eventDidMount: function (info) {
      const registered = info.event.extendedProps.registeredVolunteers;
      const required = info.event.extendedProps.requiredVolunteers;
      const volunteerIds = info.event.extendedProps.volunteerIds || [];
      const calendarElUserId = calendarEl.getAttribute("data-user-id");
      const currentUserId = calendarElUserId ? parseInt(calendarElUserId) : null;

      // Applique la couleur grise aux événements passés
      const now = new Date();
      const eventEnd = info.event.end;
      if (eventEnd && eventEnd < now) {
        info.el.style.backgroundColor = PAST_EVENTS;
        info.el.style.borderColor = PAST_EVENTS;
        return;
      }

      //Gestion des couleurs de l'événement en fonction du statut d'inscription
      if (currentUserId && volunteerIds.includes(currentUserId)) {
        info.el.style.backgroundColor = USER_REGISTERED;
        info.el.style.borderColor = USER_REGISTERED;
      } else {
        const color = registered >= required ? UNAVAILABLE : AVAILABLE;
        info.el.style.backgroundColor = color;
        info.el.style.borderColor = color;
      }
    },

    eventClick: function (info) {
      const summaryModal = document.querySelector(".eventSummaryModal");

      // Données
      const requiredVolunteers = info.event.extendedProps.requiredVolunteers;
      let volunteerIds = info.event.extendedProps.volunteerIds;
      const currentUserId = parseInt(calendarEl.getAttribute("data-user-id"));
      const currentUserFirstName = calendarEl.getAttribute("data-user-firstname");
      const now = new Date();
      const eventEnd = info.event.end;

      // Etats
      const isEventFull = volunteerIds.length >= requiredVolunteers;
      const isEventPast = eventEnd < now;
      let isUserRegistered = volunteerIds.includes(currentUserId);
      const showFullMessage = isEventFull && !isUserRegistered;

      const oldBtn = summaryModal.querySelector(".toggleRegistrationBtn");
      if (oldBtn) oldBtn.remove();

      // Elements de la modale
      const modalTitle = summaryModal.querySelector(".modalTitle");
      const registeredVolunteers = summaryModal.querySelector(".modalRegisteredVolunteers");
      const volunteerList = summaryModal.querySelector(".modalVolunteerList");
      const modalTime = summaryModal.querySelector(".modalTime");
      const modalDescription = summaryModal.querySelector(".modalDescription");
      const modalLocation = summaryModal.querySelector(".modalLocation");

      // Remplissage des informations de base
      modalTitle.textContent = info.event.title;

      if (info.event.extendedProps.description) {
        modalDescription.innerHTML = `<i class="ph-fill ph-note"></i>${info.event.extendedProps.description}`;
        modalDescription.style.display = "";
      } else {
        modalDescription.innerHTML = "";
        modalDescription.style.display = "none";
      }

      if (info.event.extendedProps.location) {
        modalLocation.innerHTML = `<i class="ph-fill ph-map-pin"></i>${info.event.extendedProps.location}`;
        modalLocation.style.display = "";
      } else {
        modalLocation.innerHTML = "";
        modalLocation.style.display = "none";
      }

      // Affichage de la date et heure
      const start = info.event.start;
      const end = info.event.end;
      const optionsDate = { weekday: "short", day: "2-digit", month: "2-digit", year: "numeric" };
      const optionsTime = { hour: "2-digit", minute: "2-digit", hour12: false };
      const dateStr = start.toLocaleDateString("fr-FR", optionsDate);
      const startTime = start.toLocaleTimeString("fr-FR", optionsTime);
      const endTime = end ? end.toLocaleTimeString("fr-FR", optionsTime) : "";
      modalTime.innerHTML = `<i class="ph ph-clock"></i>${dateStr} ${startTime} - ${endTime}`;

      // création du bouton d'inscription/désinscription uniquement pour les événements futurs
      let toggleRegistrationBtn;
      if (!isEventPast && (!isEventFull || isUserRegistered)) {
        toggleRegistrationBtn = document.createElement("button");
        toggleRegistrationBtn.className = "toggleRegistrationBtn button-primary";
        toggleRegistrationBtn.setAttribute("data-session-id", info.event.id);
        summaryModal.querySelector(".eventModal-content").appendChild(toggleRegistrationBtn);
      }

      if (showFullMessage) {
        // ===========================
        // CAS : ÉVÉNEMENT COMPLET
        // ===========================

        registeredVolunteers.style.display = "none";
        volunteerList.style.display = "none";

        let fullMessage = summaryModal.querySelector(".fullEventMessage");
        if (!fullMessage) {
          fullMessage = document.createElement("p");
          fullMessage.className = "fullEventMessage";
          summaryModal.querySelector(".eventModal-content").appendChild(fullMessage);
        }
        fullMessage.innerHTML = `<i class="ph-fill ph-warning-circle"></i>Cet événement est déjà complet, vous ne pouvez pas vous inscrire.`;
        fullMessage.style.display = "";
        fullMessage.style.color = ALERT_RED;
      } else {
        // ===========================
        // CAS : ÉVÉNEMENT NORMAL OU UTILISATEUR DÉJÀ INSCRIT
        // ===========================

        registeredVolunteers.style.display = "";
        volunteerList.style.display = "";

        const fullMessage = summaryModal.querySelector(".fullEventMessage");
        if (fullMessage) {
          fullMessage.style.display = "none";
        }

        let volunteerNames = info.event.extendedProps.volunteerFirstNames;

        function updateButtonState(button, isRegistered) {
          if (!button) return; // évite l'erreur
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

        if (toggleRegistrationBtn) {
          updateButtonState(toggleRegistrationBtn, isUserRegistered);
          toggleRegistrationBtn.onclick = async () => {
            if (isUserRegistered) {
              const result = await unsubscribeFromSession(info.event.id);
              if (result?.success) {
                isUserRegistered = false;
                volunteerIds = volunteerIds.filter((id) => id !== currentUserId);
                volunteerNames = volunteerNames.filter((name) => name !== currentUserFirstName);

                const color = volunteerIds.length >= requiredVolunteers ? UNAVAILABLE : AVAILABLE;
                info.el.style.backgroundColor = color;
                info.el.style.borderColor = color;

                info.event.setExtendedProp("volunteerIds", volunteerIds);
                info.event.setExtendedProp("volunteerFirstNames", volunteerNames);
                info.event.setExtendedProp("registeredVolunteers", volunteerIds.length);
              }
            } else {
              const result = await registerForSession(info.event.id);
              if (result?.success) {
                isUserRegistered = true;
                if (!volunteerIds.includes(currentUserId)) volunteerIds.push(currentUserId);
                if (!volunteerNames.includes(currentUserFirstName)) volunteerNames.push(currentUserFirstName);

                info.el.style.backgroundColor = USER_REGISTERED;
                info.el.style.borderColor = USER_REGISTERED;

                info.event.setExtendedProp("volunteerIds", volunteerIds);
                info.event.setExtendedProp("volunteerFirstNames", volunteerNames);
                info.event.setExtendedProp("registeredVolunteers", volunteerIds.length);
              }
            }
            updateVolunteerDisplay();
          };
        }

        updateVolunteerDisplay();
      }

      summaryModal.showModal();
    },
  });

  calendar.render();

  // ===========================
  // GESTION DE LA FERMETURE DES MODALES
  // ===========================
  bindCloseButton(".eventSummaryModal", ".eventSummaryModal .closeButton");
});
