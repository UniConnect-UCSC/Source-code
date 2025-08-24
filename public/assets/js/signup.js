// Show error message under a field
function showError(fieldId, message) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "-error");
  field.classList.add("error");
  field.classList.remove("success");
  errorDiv.textContent = message;
  errorDiv.classList.add("show");
}

// Show success state for a field
function showSuccess(fieldId) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "-error");
  field.classList.remove("error");
  field.classList.add("success");
  errorDiv.textContent = "";
  errorDiv.classList.remove("show");
}

// Validate all fields on form submit
function validateSignup() {
  const fName = document.getElementById("fName").value.trim();
  const lName = document.getElementById("lName").value.trim();
  const birthday = document.getElementById("birthday").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const confirmPassword = document
    .getElementById("confirmPassword")
    .value.trim();

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  let valid = true;

  if (!fName) {
    showError("fName", "First name is required");
    valid = false;
  } else {
    showSuccess("fName");
  }
  if (!lName) {
    showError("lName", "Last name is required");
    valid = false;
  } else {
    showSuccess("lName");
  }
  if (!birthday) {
    showError("birthday", "Birthday is required");
    valid = false;
  } else {
    showSuccess("birthday");
  }
  if (!email) {
    showError("email", "University Email is required");
    valid = false;
  } else if (!emailRegex.test(email)) {
    showError("email", "Please enter a valid email");
    valid = false;
  } else {
    showSuccess("email");
  }
  if (!password) {
    showError("password", "Password is required");
    valid = false;
  } else {
    showSuccess("password");
  }

  if (!confirmPassword) {
    showError("confirmPassword", "Please confirm your password");
    valid = false;
  } else if (password !== confirmPassword) {
    showError("confirmPassword", "Passwords do not match");
    valid = false;
  } else {
    showSuccess("confirmPassword");
  }

  return valid;
}

// Live validation on input fields
function setupLiveValidation(fieldIds) {
  fieldIds.forEach((id) => {
    const input = document.getElementById(id);
    input.addEventListener("input", () => {
      const value = input.value.trim();

      // Reset error/success
      input.classList.remove("error");
      input.classList.remove("success");
      const errorDiv = document.getElementById(id + "-error");
      errorDiv.textContent = "";
      errorDiv.classList.remove("show");

      // For confirmPassword, check live if it matches password
      if (id === "confirmPassword") {
        const password = document.getElementById("password").value.trim();
        if (value && value !== password) {
          showError("confirmPassword", "Passwords do not match");
        } else if (value && value === password) {
          showSuccess("confirmPassword");
        }
      }
    });
  });
}

// Initialize form validation
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("signupForm");

  // Submit event
  form.addEventListener("submit", function (e) {
    if (!validateSignup()) e.preventDefault();
  });

  // Live input validation
  setupLiveValidation([
    "fName",
    "lName",
    "birthday",
    "email",
    "password",
    "confirmPassword",
  ]);
});
