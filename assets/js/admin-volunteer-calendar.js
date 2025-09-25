import { syncMinDate } from "./utils.js";

// =======================
// API REQUESTS (AJAX)
// =======================
async function submitEventForm(calendar) {
  const form = document.querySelector(".createEventModal form");
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    const response = await fetch("/tableau-de-bord/planning-benevolat/creer", {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });

    const data = await response.json();

    if (data.success) {
      calendar.refetchEvents(); // recharge depuis /api/events
      form.reset();
      document.querySelector(".createEventModal").close();
    } else {
      alert("Erreur lors de la création");
    }
  });
}

async function updateEventForm(calendar) {
  const editModal = document.querySelector(".editEventModal");
  const form = editModal.querySelector("form");
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    // Récupère l'id stocké dans la modale
    const eventId = editModal.dataset.eventId;

    try {
      const response = await fetch(`/tableau-de-bord/planning-benevolat/schedule/${eventId}/edit`, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      const data = await response.json();

      if (data.success) {
        calendar.refetchEvents(); // Recharge la grille
        form.reset();
        editModal.close();
      } else {
        alert("Erreur lors de la modification");
      }
    } catch (error) {
      //pour test, à enlever ensuite
      alert("Erreur réseau");
      console.error(error);
    }
  });
}

async function deleteCalendarEvent(eventId, eventObj, modal) {
  //pour tester, à enlever ensuite (créer modale de confirmation)
  if (!confirm("Voulez-vous vraiment supprimer cet événement ?")) return;

  try {
    const response = await fetch(`/tableau-de-bord/planning-benevolat/schedule/${eventId}/delete`, {
      method: "DELETE",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    const data = await response.json();
    if (data.success) {
      console.log("Événement supprimé avec succès");
      eventObj.remove(); // Supprime visuellement de la grille
      if (modal) modal.close();
    } else {
      alert("Erreur lors de la suppression");
    }
  } catch (error) {
    alert("Erreur réseau");
    console.error(error);
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
  }
}

function toggleUntilDate(recurrenceSelect, untilDateField) {
  const formGroup = untilDateField.closest(".form-group");
  const startDateField = document.querySelector("#volunteer_session_from_date");
  untilDateField.value = "";
  if (
    recurrenceSelect.value === "daily" ||
    recurrenceSelect.value === "weekly" ||
    recurrenceSelect.value === "monthly"
  ) {
    formGroup.style.display = "block";
    untilDateField.required = true;

    if (startDateField) {
      syncMinDate(startDateField, untilDateField);
    }
  } else {
    formGroup.style.display = "none";
    untilDateField.required = false;
  }
}

// =======================
// CALENDAR INSTANCE & CONFIGURATION
// =======================

document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    locale: "fr",
    height: "auto",
    // eventColor: "#339d95",
    // eventColor: "#c9e8e5",

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

    events: "/tableau-de-bord/planning-benevolat/sessions",

    selectable: true,
    selectMirror: true, // Affichage en temps réel de la sélection

    // SELECTION D'UNE DATE
    dateClick: function (info) {
      const createModal = document.querySelector(".createEventModal");
      const form = createModal.querySelector("form");
      const recurrenceSelect = document.querySelector("#volunteer_session_recurrence");
      const untilDateField = document.querySelector("#volunteer_session_until_date");
      const startDateInput = form.querySelector("#volunteer_session_from_date");
      const startTimeInput = form.querySelector("#volunteer_session_from_time");
      const endDateInput = form.querySelector("#volunteer_session_to_date");
      const endTimeInput = form.querySelector("#volunteer_session_to_time");

      startDateInput.value = info.dateStr;
      endDateInput.value = info.dateStr;

      if (startDateInput && endDateInput) {
        syncMinDate(startDateInput, endDateInput);
      }

      startTimeInput.value = "09:00";

      function updateEndTime() {
        const [hours, minutes] = startTimeInput.value.split(":").map(Number);
        const endDateObj = new Date();
        endDateObj.setHours(hours + 3, minutes); // ajoute 3h

        const pad = (num) => num.toString().padStart(2, "0");
        startDateInput.addEventListener("input", () => (endDateInput.value = startDateInput.value));
        endTimeInput.value = `${pad(endDateObj.getHours())}:${pad(endDateObj.getMinutes())}`;
      }

      startTimeInput.addEventListener("input", updateEndTime);

      updateEndTime();

      createModal.showModal();

      if (recurrenceSelect && untilDateField) {
        toggleUntilDate(recurrenceSelect, untilDateField);
        recurrenceSelect.addEventListener("change", () => toggleUntilDate(recurrenceSelect, untilDateField));
      }
    },

    //SELECTION D'UNE PLAGE DE DATES
    select: function (info) {
      const createModal = document.querySelector(".createEventModal");
      const closeButton = createModal.querySelector(".closeButton");

      createModal.showModal();

      const startDate = info.start;
      const endDate = info.end;

      document.getElementById("volunteer_session_from_date").value = startDate.toISOString().slice(0, 10);
      document.getElementById("volunteer_session_from_time").value = "09:00";
      document.getElementById("volunteer_session_to_date").value = endDate.toISOString().slice(0, 10);
      document.getElementById("volunteer_session_to_time").value = "17:00";

      closeButton.addEventListener("click", () => {
        createModal.close();
      });
    },

    eventClick: function (info) {
      const summaryModal = document.querySelector(".eventSummaryModal");

      // ===========================
      // AFFICHER LE RÉSUMÉ D'UN ÉVÉNEMENT
      // ===========================
      const requiredVolunteers = info.event.extendedProps.requiredVolunteers;
      const modalTitle = summaryModal.querySelector(".modalTitle");
      const registeredVolunteers = summaryModal.querySelector(".modalRegisteredVolunteers");
      const volunteerList = summaryModal.querySelector(".modalVolunteerList");
      const modalTime = summaryModal.querySelector(".modalTime");
      const volunteerNames = info.event.extendedProps.volunteerFirstNames;

      modalTitle.textContent = info.event.title;
      registeredVolunteers.textContent = `Bénvoles inscrits : ${info.event.extendedProps.registeredVolunteers} / ${requiredVolunteers}`;
      volunteerList.innerHTML = "";
      volunteerNames.forEach((name) => {
        volunteerList.appendChild(document.createElement("li")).textContent = name;
      });

      //affiche le résumé de la date en français
      const start = info.event.start;
      const end = info.event.end;
      const optionsDate = { weekday: "short", day: "2-digit", month: "2-digit", year: "numeric" };
      const optionsTime = { hour: "2-digit", minute: "2-digit", hour12: false };
      const dateStr = start.toLocaleDateString("fr-FR", optionsDate);
      const startTime = start.toLocaleTimeString("fr-FR", optionsTime);
      const endTime = end ? end.toLocaleTimeString("fr-FR", optionsTime) : "";
      const timeText = `<i class="ph ph-clock"></i>${dateStr} ${startTime} - ${endTime}`;
      modalTime.innerHTML = timeText;

      summaryModal.showModal();

      /* ===========================
         EDITER L'ÉVÉNEMENT
         =========================== */

      const editModal = document.querySelector(".editEventModal");
      const editButton = document.querySelector(".eventSummaryModal .editEventButton");

      const editForm = editModal.querySelector("form");

      editButton.onclick = () => {
        summaryModal.close();

        // Pré-remplissage avec les données de l'événement
        editForm.querySelector('[name="volunteer_session_edit[title]"]').value = info.event.title;
        editForm.querySelector('[name="volunteer_session_edit[description]"]').value =
          info.event.extendedProps.description || "";
        editForm.querySelector('[name="volunteer_session_edit[location]"]').value =
          info.event.extendedProps.location || "";
        // Remplir les champs date et heure de début
        if (info.event.start) {
          const startDate = info.event.start.toISOString().slice(0, 10);
          const startTime = info.event.start.toTimeString().slice(0, 5);
          editForm.querySelector('[name="volunteer_session_edit[from_date]"]').value = startDate;
          editForm.querySelector('[name="volunteer_session_edit[from_time]"]').value = startTime;
        }
        // Remplir les champs date et heure de fin
        if (info.event.end) {
          const endDate = info.event.end.toISOString().slice(0, 10);
          const endTime = info.event.end.toTimeString().slice(0, 5);
          editForm.querySelector('[name="volunteer_session_edit[to_date]"]').value = endDate;
          editForm.querySelector('[name="volunteer_session_edit[to_time]"]').value = endTime;
        }
        editForm.querySelector('[name="volunteer_session_edit[required_volunteers]"]').value =
          info.event.extendedProps.requiredVolunteers;

        editModal.dataset.eventId = info.event.id; // Stocke l'ID de l'événement dans un data-attribute
        syncMinDate(
          editForm.querySelector('[name="volunteer_session_edit[from_date]"]'),
          editForm.querySelector('[name="volunteer_session_edit[to_date]"]')
        );
        editModal.showModal();
      };

      /* ===========================
         SUPPRIMER L'ÉVÉNEMENT
         =========================== */

      const deleteEventButton = editModal.querySelector(".deleteEventButton");

      deleteEventButton.onclick = () => {
        deleteCalendarEvent(info.event.id, info.event, summaryModal);
        editModal.close();
      };

      /* ===========================
        GESTION DE LA FERMETURE DES MODALES
         =========================== */
      bindCloseButton(".createEventModal", ".createEventModal .closeButton");
      bindCloseButton(".eventSummaryModal", ".eventSummaryModal .closeButton");
      bindCloseButton(".editEventModal", ".editEventModal .closeButton");
    },
  });

  calendar.render();
  submitEventForm(calendar);
  updateEventForm(calendar);
});

// Glisser-déposer des événements
// editable: true,

//DEPLACEMENT DES ÉVÉNEMENTS DANS LE CALENDRIER
// eventDrop: function (info) {},
