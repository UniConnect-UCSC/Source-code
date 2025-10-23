function openSellItemModal() {
  const modal = document.getElementById("sell-item-modal");
  modal.style.zIndex = "1000";
  modal.style.pointerEvents = "auto";
  document.body.style.overflow = "hidden";
  gsap.set(modal, { opacity: 0 });
  gsap.to(modal, {
    duration: 0.3,
    ease: "power2.out",
    opacity: 1,
  });
}

function closeSellItemModal() {
  const modal = document.getElementById("sell-item-modal");
  gsap.to(modal, {
    duration: 0.25,
    ease: "power2.in",
    opacity: 0,
    onComplete: () => {
      modal.style.zIndex = "-1";
      document.body.style.overflow = "auto";
      modal.style.pointerEvents = "none";
      // Reset preview
      const previewImg = document.getElementById("sell-item-preview-img");
      if (previewImg) {
        previewImg.src = "";
        previewImg.style.display = "none";
      }
      // Reset form
      const form = document.getElementById("sell-item-form");
      if (form) form.reset();
    },
  });
}

function openSellItemImageSelector() {
  const photosInput = document.getElementById("sell-item-media-input");
  if (photosInput) {
    console.log("clicking");
    photosInput.click();
  }
}

// Preview selected image
document.addEventListener("DOMContentLoaded", function () {
  const mediaInput = document.getElementById("sell-item-media-input");
  const previewImg = document.getElementById("sell-item-preview-img");
  if (mediaInput && previewImg) {
    mediaInput.addEventListener("change", function () {
      const file = mediaInput.files[0];
      if (file) {
        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = "block";
        previewImg.onload = () => URL.revokeObjectURL(previewImg.src);
      } else {
        previewImg.src = "";
        previewImg.style.display = "none";
      }
    });
  }
});

//Form Validation

function showMarketError(fieldId, message) {
  const field = document.getElementById(fieldId);
  let errorDiv;
  switch (fieldId) {
    case "item-title":
      errorDiv = document.getElementById("sell-item-title-error");
      break;
    case "item-description":
      errorDiv = document.getElementById("sell-item-description-error");
      break;
    case "item-price":
      errorDiv = document.getElementById("sell-item-price-error");
      break;
    case "item-category":
      errorDiv = document.getElementById("sell-item-category-error");
      break;
    case "sell-item-media-input":
      errorDiv = document.getElementById("sell-item-media-error");
      break;
    default:
      errorDiv = null;
  }
  if (field) {
    field.classList.add("error");
    field.classList.remove("success");
  }
  if (errorDiv) {
    errorDiv.textContent = message;
    errorDiv.classList.add("show");
  }
}

function showMarketSuccess(fieldId) {
  const field = document.getElementById(fieldId);
  let errorDiv;
  switch (fieldId) {
    case "item-title":
      errorDiv = document.getElementById("sell-item-title-error");
      break;
    case "item-description":
      errorDiv = document.getElementById("sell-item-description-error");
      break;
    case "item-price":
      errorDiv = document.getElementById("sell-item-price-error");
      break;
    case "item-category":
      errorDiv = document.getElementById("sell-item-category-error");
      break;
    case "sell-item-media-input":
      errorDiv = document.getElementById("sell-item-media-error");
      break;
    default:
      errorDiv = null;
  }
  if (field) {
    field.classList.remove("error");
    field.classList.add("success");
  }
  if (errorDiv) {
    errorDiv.textContent = "";
    errorDiv.classList.remove("show");
  }
}

// Validate all fields on form submit
function validateSellItem() {
  const title = document.getElementById("item-title").value.trim();
  const description = document.getElementById("item-description").value.trim();
  const price = document.getElementById("item-price").value.trim();
  const category = document.getElementById("item-category").value;
  const media = document.getElementById("sell-item-media-input").files[0];
  let valid = true;

  if (!title) {
    showMarketError("item-title", "Title is required");
    valid = false;
  } else {
    showMarketSuccess("item-title");
  }
  if (!description) {
    showMarketError("item-description", "Description is required");
    valid = false;
  } else {
    showMarketSuccess("item-description");
  }
  if (!price) {
    showMarketError("item-price", "Price is required");
    valid = false;
  } else {
    showMarketSuccess("item-price");
  }
  if (!category) {
    showMarketError("item-category", "Category is required");
    valid = false;
  } else {
    showMarketSuccess("item-category");
  }
  if (!media) {
    showMarketError("sell-item-media-input", "Image is required");
    valid = false;
  } else {
    showMarketSuccess("sell-item-media-input");
  }
  return valid;
}

// Live validation on input fields
function setupMarketLiveValidation(fieldIds) {
  fieldIds.forEach((id) => {
    const input = document.getElementById(id);
    input.addEventListener("input", () => {
      input.classList.remove("error");
      input.classList.remove("success");
      let errorDiv;
      switch (id) {
        case "item-title":
          errorDiv = document.getElementById("sell-item-title-error");
          break;
        case "item-description":
          errorDiv = document.getElementById("sell-item-description-error");
          break;
        case "item-price":
          errorDiv = document.getElementById("sell-item-price-error");
          break;
        case "item-category":
          errorDiv = document.getElementById("sell-item-category-error");
          break;
        default:
          errorDiv = null;
      }
      if (errorDiv) {
        errorDiv.textContent = "";
        errorDiv.classList.remove("show");
      }
    });
  });
  // For file input
  const mediaInput = document.getElementById("sell-item-media-input");
  mediaInput.addEventListener("change", () => {
    mediaInput.classList.remove("error");
    mediaInput.classList.remove("success");
    const errorDiv = document.getElementById("sell-item-media-error");
    if (errorDiv) {
      errorDiv.textContent = "";
      errorDiv.classList.remove("show");
    }
  });
}

// SINGLE DOMContentLoaded event listener
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("sell-item-form");
  if (!form) return;

  // Live validation
  setupMarketLiveValidation([
    "item-title",
    "item-description",
    "item-price",
    "item-category",
  ]);

  // AJAX submit for selling item
  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default first

    // Run validation
    if (!validateSellItem()) {
      return false;
    }

    const spinner = document.getElementById("sell-item-loading-spinner");
    if (spinner) spinner.style.display = "flex";

    const photosInput = document.getElementById("sell-item-media-input");
    const file =
      photosInput && photosInput.files && photosInput.files[0]
        ? photosInput.files[0]
        : null;

    const formData = new FormData(form);
    if (file) {
      formData.set("media", file); // Use set instead of append to avoid duplicates
    }

    fetch("/marketplace/createItem", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((res) => {
        console.log("Response status:", res.status);
        console.log("Response ok:", res.ok);
        return res.text(); // Get as text first to see what's returned
      })
      .then((text) => {
        console.log("Response text:", text);
        try {
          const data = JSON.parse(text);
          if (data.success) {
            closeSellItemModal();
            window.location.reload();
          } else {
            alert("Error: " + (data.message || "Unknown error"));
          }
        } catch (parseError) {
          console.error("JSON parse error:", parseError);
          console.error("Response was:", text);
          alert("Server error. Check console for details.");
        }
      })
      .catch((err) => {
        console.error("Fetch error:", err);
        alert("Error creating item: " + err.message);
      })
      .finally(() => {
        if (spinner) spinner.style.display = "none";
      });
  });
});
