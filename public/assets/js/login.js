function showError(fieldId, message) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "-error");
  field.classList.add("error");
  field.classList.remove("success");
  errorDiv.textContent = message;
  errorDiv.classList.add("show");
}

function showSuccess(fieldId) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "-error");
  field.classList.remove("error");
  field.classList.remove("success");
  errorDiv.textContent = "";
  errorDiv.classList.remove("show");
}

function validateLogin() {
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  let valid = true;

  if (!email || !emailRegex.test(email)) {
    showError("email", "Please enter a valid email address");
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

  return valid;
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  form.addEventListener("submit", function (e) {
    if (!validateLogin()) {
      e.preventDefault();
    }
  });

  // Clear error when user starts typing in password field
  const passwordInput = document.getElementById("password");
  passwordInput.addEventListener("input", function () {
    const errorDiv = document.getElementById("password-error");
    errorDiv.textContent = "";
    errorDiv.classList.remove("show");
    passwordInput.classList.remove("error");
    passwordInput.classList.remove("success");
    // Also clear server-side error if present
    const incorrectError = document.getElementById("password-incorrect-error");
    if (incorrectError) {
      incorrectError.textContent = "";
      incorrectError.classList.remove("show");
    }
  });

  const emailInput = document.getElementById("email");
  emailInput.addEventListener("input", function () {
    const errorDiv = document.getElementById("email-error");
    errorDiv.textContent = "";
    errorDiv.classList.remove("show");
    emailInput.classList.remove("error");
    emailInput.classList.remove("success");

    // Also clear server-side error if present
    const incorrectError = document.getElementById("email-incorrect-error");
    if (incorrectError) {
      incorrectError.textContent = "";
      incorrectError.classList.remove("show");
    }
  });
});
