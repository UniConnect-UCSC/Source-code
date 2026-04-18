(function () {
  function getEditableFields(form) {
    return form.querySelectorAll(
      "input:not([type='hidden']), textarea, select",
    );
  }

  function snapshotInitialValues(form) {
    getEditableFields(form).forEach((field) => {
      if (field.type === "file") {
        field.dataset.initialValue = "";
        return;
      }
      field.dataset.initialValue = field.value;
    });
  }

  function setFormEditable(form, editable) {
    getEditableFields(form).forEach((field) => {
      if (field.type === "file") {
        field.disabled = !editable;
        return;
      }

      if (editable) {
        field.removeAttribute("readonly");
        field.disabled = false;
      } else if (
        field.tagName === "TEXTAREA" ||
        field.type === "text" ||
        field.type === "password" ||
        field.type === "date"
      ) {
        field.setAttribute("readonly", "readonly");
      }
    });
  }

  function setActionButtons(form, mode) {
    const editBtn = form.querySelector(".settings-edit-btn");
    const cancelBtn = form.querySelector(".settings-cancel-btn");
    const saveBtn = form.querySelector(".settings-save-btn");

    if (mode === "edit") {
      if (editBtn) editBtn.hidden = true;
      if (cancelBtn) cancelBtn.hidden = false;
      if (saveBtn) saveBtn.hidden = false;
      return;
    }

    if (editBtn) editBtn.hidden = false;
    if (cancelBtn) cancelBtn.hidden = true;
    if (saveBtn) saveBtn.hidden = true;
  }

  function enterEditMode(form) {
    snapshotInitialValues(form);
    setFormEditable(form, true);
    setActionButtons(form, "edit");

    const firstField = form.querySelector(
      "input:not([type='hidden']):not([type='file']), textarea, select",
    );
    if (firstField) firstField.focus();
  }

  function cancelEditMode(form) {
    getEditableFields(form).forEach((field) => {
      if (field.type === "file") {
        field.value = "";
        field.disabled = true;
        return;
      }

      if (field.dataset.initialValue !== undefined) {
        field.value = field.dataset.initialValue;
      }
    });

    setFormEditable(form, false);
    setActionButtons(form, "view");
  }

  function updateSnapshot(form) {
    getEditableFields(form).forEach((field) => {
      if (field.type !== "file") {
        field.dataset.initialValue = field.value;
      }
    });
  }

  async function submitSettingsForm(form) {
    const action = form.dataset.action;
    if (!action) {
      throw new Error("Missing form action.");
    }

    const fileInput = form.querySelector('input[type="file"]');
    const hasSelectedFile = !!(
      fileInput &&
      fileInput.files &&
      fileInput.files.length > 0
    );

    let response;

    if (hasSelectedFile) {
      const formData = new FormData(form);
      formData.append("action", action);
      response = await Ajax.request("POST", "/settings", formData);
    } else {
      const payload = Object.fromEntries(new FormData(form).entries());
      payload.action = action;
      response = await Ajax.request("POST", "/settings", payload, {
        "Content-Type": "application/json",
      });
    }

    if (!response || !response.success) {
      throw new Error(response?.message || "Update failed.");
    }

    return response;
  }

  function showFlash(message, type) {
    const alertBox = document.querySelector(".settings-alert");
    if (!alertBox) return;

    alertBox.textContent = message;
    alertBox.classList.remove("success", "error");
    alertBox.classList.add(type === "success" ? "success" : "error");

    // Auto hide after 4 seconds
    setTimeout(() => {
      alertBox.textContent = "";
      alertBox.classList.remove("success", "error");
    }, 4000);
  }

  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".settings-form").forEach((form) => {
      setFormEditable(form, false);
      setActionButtons(form, "view");
      snapshotInitialValues(form);

      const editBtn = form.querySelector(".settings-edit-btn");
      const cancelBtn = form.querySelector(".settings-cancel-btn");

      if (editBtn) {
        editBtn.addEventListener("click", () => enterEditMode(form));
      }

      if (cancelBtn) {
        cancelBtn.addEventListener("click", () => cancelEditMode(form));
      }

      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const saveBtn = form.querySelector(".settings-save-btn");
        if (saveBtn) saveBtn.disabled = true;

        try {
          const response = await submitSettingsForm(form);
          updateSnapshot(form); // Update snapshot so cancel doesn't revert to old value
          cancelEditMode(form);
          showFlash(response.message || "Updated successfully.", "success");
        } catch (error) {
          showFlash(error.message || "Update failed.", "error");
          console.error(error);
        } finally {
          if (saveBtn) saveBtn.disabled = false;
        }
      });
    });
  });
})();
