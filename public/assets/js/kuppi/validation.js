// Kuppi form validation utilities
(function() {
  function showKuppiError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + "-error");
    if (!field || !errorDiv) return;
    field.classList.add("kuppi-error");
    field.classList.remove("kuppi-success");
    errorDiv.textContent = message;
    errorDiv.classList.add("show");
  }

  function showKuppiSuccess(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + "-error");
    if (!field || !errorDiv) return;
    field.classList.remove("kuppi-error");
    field.classList.remove("kuppi-success");
    errorDiv.textContent = "";
    errorDiv.classList.remove("show");
  }

  function clearKuppiError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + "-error");
    if (!field || !errorDiv) return;
    field.classList.remove("kuppi-error");
    errorDiv.textContent = "";
    errorDiv.classList.remove("show");
  }

  // Reusable date/time validation for any kuppi form
  function addDateTimeValidation() {
    const form = document.querySelector('#kuppiModalBody form');
    if (!form) return;

    const dateInput = form.querySelector('#date');
    const timeInput = form.querySelector('#time');

    if (dateInput) {
      dateInput.addEventListener('input', function() {
        clearKuppiError('date');
        clearKuppiError('time');
      });
    }

    if (timeInput) {
      timeInput.addEventListener('input', function() {
        clearKuppiError('date');
        clearKuppiError('time');
      });
    }

    form.addEventListener('submit', function(e) {
      if (!dateInput || !timeInput) return;

      const dateValue = dateInput.value;
      const timeValue = timeInput.value;
      if (!dateValue || !timeValue) return;

      const selectedDateTime = new Date(dateValue + 'T' + timeValue);
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const selectedDate = new Date(selectedDateTime.getFullYear(), selectedDateTime.getMonth(), selectedDateTime.getDate());

      if (selectedDateTime <= now) {
        e.preventDefault();

        if (selectedDate.getTime() === today.getTime()) {
          showKuppiError('time', 'Please select a future time.');
        } else if (selectedDate < today) {
          showKuppiError('date', 'Please select a future date.');
        } else {
          showKuppiError('date', 'Please select a future date and time.');
        }
      }
    });
  }

  // Make functions globally available
  window.showKuppiError = showKuppiError;
  window.showKuppiSuccess = showKuppiSuccess;
  window.clearKuppiError = clearKuppiError;
  window.addDateTimeValidation = addDateTimeValidation;
})();
