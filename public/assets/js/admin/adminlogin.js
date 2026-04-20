function showError(fieldId, message) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "-error");
  if (!field || !errorDiv) return;

  field.classList.add("error");
  field.classList.remove("success");
  errorDiv.textContent = message;
  errorDiv.classList.add("show");
}

function showSuccess(fieldId) {
  const field = document.getElementById(fieldId);
  const errorDiv = document.getElementById(fieldId + "-error");
  if (!field || !errorDiv) return;

  field.classList.remove("error");
  field.classList.remove("success");
  errorDiv.textContent = "";
  errorDiv.classList.remove("show");
}

function validateAdminLogin() {
  const email = document.getElementById("admin-email")?.value.trim() || "";
  const password =
    document.getElementById("admin-password")?.value.trim() || "";
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  let valid = true;

  if (!email || !emailRegex.test(email)) {
    showError("admin-email", "Please enter a valid email address");
    valid = false;
  } else {
    showSuccess("admin-email");
  }

  if (!password) {
    showError("admin-password", "Password is required");
    valid = false;
  } else {
    showSuccess("admin-password");
  }

  return valid;
}

function showAuthError(message) {
  const authError = document.getElementById("admin-auth-error");
  if (authError) {
    authError.textContent = message;
    authError.classList.add("show");
  }
}

function clearAuthError() {
  const authError = document.getElementById("admin-auth-error");
  if (authError) {
    authError.textContent = "";
    authError.classList.remove("show");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("adminLoginForm");
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    clearAuthError();

    if (!validateAdminLogin()) return;

    const email = document.getElementById("admin-email").value.trim();
    const password = document.getElementById("admin-password").value.trim();
    const submitBtn = form.querySelector("button[type='submit']");

    if (submitBtn) submitBtn.disabled = true;

    try {
      const res = await Ajax.jsonPost("/admin/loginAdmin", { email, password });

      if (res?.success) {
        window.location.href = res.redirect ?? "/admin/dashboard";
      } else {
        showAuthError(res?.message || "Invalid email or password.");
      }
    } catch (e) {
      console.error(e);
      showAuthError("An error occurred. Please try again.");
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });

  const passwordInput = document.getElementById("admin-password");
  if (passwordInput) {
    passwordInput.addEventListener("input", function () {
      showSuccess("admin-password");
      clearAuthError();
    });
  }

  const emailInput = document.getElementById("admin-email");
  if (emailInput) {
    emailInput.addEventListener("input", function () {
      showSuccess("admin-email");
      clearAuthError();
    });
  }
});
