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
      // Reset previews
      const previewGrid = document.getElementById("sell-item-preview-grid");
      if (previewGrid) {
        previewGrid.innerHTML = "";
      }
      const mediaInput = document.getElementById("sell-item-media-input");
      if (mediaInput) {
        mediaInput.value = "";
        mediaInput._selectedFiles = new DataTransfer();
        mediaInput.dataset.selectionOverflow = "0";
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
  const previewGrid = document.getElementById("sell-item-preview-grid");
  if (mediaInput && previewGrid) {
    mediaInput._selectedFiles = new DataTransfer();
    mediaInput.dataset.selectionOverflow = "0";

    const renderSellItemPreview = () => {
      previewGrid.innerHTML = "";
      const files = Array.from((mediaInput._selectedFiles && mediaInput._selectedFiles.files) || []);
      files.forEach((file) => {
        const img = document.createElement("img");
        const url = URL.createObjectURL(file);
        img.src = url;
        img.onload = () => URL.revokeObjectURL(url);
        previewGrid.appendChild(img);
      });
    };

    mediaInput.addEventListener("change", function () {
      const incomingFiles = Array.from(mediaInput.files || []);
      const dataTransfer = mediaInput._selectedFiles || new DataTransfer();
      let rejectedCount = 0;

      incomingFiles.forEach((file) => {
        if (dataTransfer.files.length < 5) {
          dataTransfer.items.add(file);
        } else {
          rejectedCount++;
        }
      });

      mediaInput._selectedFiles = dataTransfer;
      mediaInput.files = dataTransfer.files;
      mediaInput.dataset.selectionOverflow = rejectedCount > 0 ? "1" : "0";

      renderSellItemPreview();

      if (rejectedCount > 0) {
        showMarketError("sell-item-media-input", "Maximum 5 images are allowed");
      } else if (dataTransfer.files.length >= 1) {
        showMarketSuccess("sell-item-media-input");
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
    case "item-status":
      errorDiv = document.getElementById("sell-item-status-error");
      break;
    case "item-contact-number":
      errorDiv = document.getElementById("sell-item-contact-number-error");
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
    case "item-status":
      errorDiv = document.getElementById("sell-item-status-error");
      break;
    case "item-contact-number":
      errorDiv = document.getElementById("sell-item-contact-number-error");
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

function normalizeContactNumber(value) {
  return String(value || "").replace(/\D+/g, "");
}

// Validate all fields on form submit
function validateSellItem() {
  const title = document.getElementById("item-title").value.trim();
  const description = document.getElementById("item-description").value.trim();
  const price = document.getElementById("item-price").value.trim();
  const category = document.getElementById("item-category").value;
  const contactInput = document.getElementById("item-contact-number");
  const contactNumber = contactInput ? contactInput.value.trim() : "";
  const mediaFiles = Array.from(
    document.getElementById("sell-item-media-input").files || []
  );
  const status = document.getElementById("item-status").value;
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
  if (mediaFiles.length < 1) {
    showMarketError("sell-item-media-input", "At least one image is required");
    valid = false;
  } else if (mediaFiles.length > 5) {
    showMarketError("sell-item-media-input", "Maximum 5 images are allowed");
    valid = false;
  } else {
    showMarketSuccess("sell-item-media-input");
  }
  if (!status) {
    showMarketError("item-status", "Status is required");
    valid = false;
  } else {
    showMarketSuccess("item-status");
  }
  if (!contactNumber) {
    showMarketError("item-contact-number", "Contact number is required");
    valid = false;
  } else {
    const normalizedContact = normalizeContactNumber(contactNumber);
    if (normalizedContact.length !== 10) {
      showMarketError("item-contact-number", "Contact number must be exactly 10 digits");
      valid = false;
    } else {
      if (contactInput) {
        contactInput.value = normalizedContact;
      }
      showMarketSuccess("item-contact-number");
    }
  }
  return valid;
}

// Live validation on input fields
function setupMarketLiveValidation(fieldIds) {
  function validateFieldLive(id, input) {
    if (!input) return;

    const rawValue = input.value;
    const value = typeof rawValue === "string" ? rawValue.trim() : rawValue;

    if (id === "item-contact-number") {
      const normalizedContact = normalizeContactNumber(value);
      input.value = normalizedContact;

      if (!normalizedContact) {
        showMarketError("item-contact-number", "Contact number is required");
        return;
      }

      if (normalizedContact.length !== 10) {
        showMarketError("item-contact-number", "Contact number must be exactly 10 digits");
        return;
      }

      showMarketSuccess("item-contact-number");
      return;
    }

    if (!value) {
      switch (id) {
        case "item-title":
          showMarketError("item-title", "Title is required");
          break;
        case "item-description":
          showMarketError("item-description", "Description is required");
          break;
        case "item-price":
          showMarketError("item-price", "Price is required");
          break;
        case "item-category":
          showMarketError("item-category", "Category is required");
          break;
        case "item-status":
          showMarketError("item-status", "Status is required");
          break;
        default:
          break;
      }
      return;
    }

    showMarketSuccess(id);
  }

  fieldIds.forEach((id) => {
    const input = document.getElementById(id);
    if (!input) return;

    const eventName = input.tagName === "SELECT" ? "change" : "input";
    input.addEventListener(eventName, () => {
      validateFieldLive(id, input);
    });
  });

  const mediaInput = document.getElementById("sell-item-media-input");
  if (mediaInput) {
    mediaInput.addEventListener("change", () => {
      if ((mediaInput.dataset.selectionOverflow || "0") === "1") {
        showMarketError("sell-item-media-input", "Maximum 5 images are allowed");
        return;
      }

      const files = Array.from(mediaInput.files || []);
      if (files.length > 5) {
        showMarketError("sell-item-media-input", "Maximum 5 images are allowed");
      } else if (files.length >= 1) {
        showMarketSuccess("sell-item-media-input");
      } else {
        mediaInput.classList.remove("error");
        mediaInput.classList.remove("success");

        const errorDiv = document.getElementById("sell-item-media-error");
        if (errorDiv) {
          errorDiv.textContent = "";
          errorDiv.classList.remove("show");
        }
      }
    });
  }
}

// SINGLE DOMContentLoaded event listener
document.addEventListener("DOMContentLoaded", function () {
  // --- Sell-item form wiring (only if present) ---
  const form = document.getElementById("sell-item-form");

  if (form) {
    setupMarketLiveValidation([
      "item-title",
      "item-description",
      "item-price",
      "item-category",
      "item-status",
      "item-contact-number"
    ]);

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      if (!validateSellItem()) {
        return false;
      }

      const spinner = document.getElementById("sell-item-loading-spinner");
      if (spinner) spinner.style.display = "flex";

      const formData = new FormData(form);

      fetch("/marketplace/createItem", {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      })
        .then((res) => res.text())
        .then((text) => {
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
  }

  // --- Marketplace filter wiring (only if filterbar present) ---
  const filterBar = document.querySelector(".marketplace-filterbar");
  const pills = filterBar
    ? Array.from(filterBar.querySelectorAll(".marketplace-category-pill"))
    : [];
  const searchInput = document.getElementById("marketplace-search-input");
  const cards = Array.from(
    document.querySelectorAll(".market-items .marketplace-item-card")
  );

  const noResultsEl = document.getElementById("marketplace-no-results");
  let activeCategory = "all";

  function applyMarketplaceFilters() {
    if (!filterBar) return;

    const q = (searchInput?.value || "").toLowerCase().trim();
    let visibleCount = 0;

    cards.forEach((card) => {
      const searchText = (card.dataset.searchText || "").toLowerCase();
      const categoryName = (card.dataset.categoryName || "").toLowerCase();

      const matchesCategory =
        activeCategory === "all" || categoryName === activeCategory;
      const matchesQuery = q === "" || searchText.includes(q);

      const isVisible = matchesCategory && matchesQuery;
      card.style.display = isVisible ? "" : "none";
      if (isVisible) visibleCount++;
    });

    if (noResultsEl) {
      noResultsEl.style.display = visibleCount === 0 ? "" : "none";
    }
  }

  if (filterBar) {
    pills.forEach((pill) => {
      pill.addEventListener("click", () => {
        pills.forEach((p) => p.classList.remove("is-active"));
        pill.classList.add("is-active");
        activeCategory = (pill.dataset.category || "all").toLowerCase();
        applyMarketplaceFilters();
      });
    });

    if (searchInput) {
      searchInput.addEventListener("input", applyMarketplaceFilters);
    }

    applyMarketplaceFilters();
  }

  // --- Saved Items sidebar (DB-backed) ---
  const savedSidebar = document.getElementById("marketplace-saved-sidebar");
  const savedBackdrop = document.getElementById("marketplace-saved-backdrop");
  const savedOpenBtn = document.getElementById("marketplace-saved-open");
  const savedCloseBtn = document.getElementById("marketplace-saved-close");
  const savedListEl = document.getElementById("marketplace-saved-list");
  const savedEmptyEl = document.getElementById("marketplace-saved-empty");

  let savedIds = new Set();
  let savedItems = [];

  const normalizeItemId = (value) => String(value ?? "").trim();

  const setSidebarOpen = (isOpen) => {
    if (!savedSidebar || !savedBackdrop) return;
    savedSidebar.classList.toggle("is-open", isOpen);
    savedBackdrop.classList.toggle("is-open", isOpen);
  };

  const escapeHtml = (s) =>
    String(s ?? "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    }[m]));

  const syncSaveButtons = () => {
    document.querySelectorAll(".marketplace-save-btn").forEach((btn) => {
      const itemId = normalizeItemId(btn.dataset.itemId);
      const isSaved = savedIds.has(itemId);
      btn.classList.toggle("is-saved", isSaved);
      btn.setAttribute("aria-pressed", isSaved ? "true" : "false");
    });
  };

  const focusMarketplaceItem = (itemId) => {
    const normalizedItemId = normalizeItemId(itemId);
    const targetCard = document.querySelector(
      `.market-items .marketplace-item-card[data-item-id="${normalizedItemId}"]`
    );

    if (!targetCard) {
      alert("This item is no longer available in Marketplace.");
      return;
    }

    const targetCategory = (targetCard.dataset.categoryName || "all").toLowerCase();

    if (targetCategory && targetCategory !== "all") {
      const matchingPill = pills.find(
        (pill) => (pill.dataset.category || "").toLowerCase() === targetCategory
      );

      pills.forEach((pill) => pill.classList.remove("is-active"));
      if (matchingPill) {
        matchingPill.classList.add("is-active");
        activeCategory = targetCategory;
      } else {
        const allPill = pills.find(
          (pill) => (pill.dataset.category || "").toLowerCase() === "all"
        );
        if (allPill) allPill.classList.add("is-active");
        activeCategory = "all";
      }
    } else {
      const allPill = pills.find(
        (pill) => (pill.dataset.category || "").toLowerCase() === "all"
      );
      pills.forEach((pill) => pill.classList.remove("is-active"));
      if (allPill) allPill.classList.add("is-active");
      activeCategory = "all";
    }

    if (searchInput) {
      searchInput.value = "";
    }
    applyMarketplaceFilters();

    targetCard.scrollIntoView({ behavior: "smooth", block: "center" });
    targetCard.style.transition = "box-shadow 0.25s ease";
    targetCard.style.boxShadow = "0 0 0 3px rgba(59, 130, 246, 0.55)";
    window.setTimeout(() => {
      targetCard.style.boxShadow = "";
    }, 1400);
  };

  const renderSaved = () => {
    if (!savedListEl || !savedEmptyEl) return;

    savedListEl.innerHTML = "";
    savedEmptyEl.style.display = savedItems.length ? "none" : "";

    savedItems.forEach((item) => {
      const normalizedItemId = normalizeItemId(item.id);
      const row = document.createElement("div");
      row.className = "marketplace-saved-row";
      row.dataset.itemId = normalizedItemId;
      row.tabIndex = 0;

      const thumb = item.image_url
        ? `<div class="marketplace-saved-thumb"><img src="${escapeHtml(item.image_url)}" alt=""></div>`
        : `<div class="marketplace-saved-thumb">No</div>`;

      row.innerHTML = `
        ${thumb}
        <div class="marketplace-saved-meta">
          <div class="marketplace-saved-title">${escapeHtml(item.title)}</div>
          <div class="marketplace-saved-price">LKR ${escapeHtml(item.price)}</div>
        </div>
        <button type="button" class="marketplace-saved-remove" data-item-id="${escapeHtml(normalizedItemId)}" aria-label="Remove saved item">
          <i data-lucide="bookmark"></i>
        </button>
      `;

      const openItemFromSaved = (e) => {
        if (e.target.closest(".marketplace-saved-remove")) return;
        setSidebarOpen(false);
        focusMarketplaceItem(normalizedItemId);
      };

      row.addEventListener("click", openItemFromSaved);
      row.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          openItemFromSaved(e);
        }
      });

      savedListEl.appendChild(row);
    });

    savedListEl.querySelectorAll(".marketplace-saved-remove").forEach((btn) => {
      btn.addEventListener("click", async (e) => {
        e.preventDefault();
        e.stopPropagation();
        await toggleSave(btn.dataset.itemId, true);
      });
    });
    

    if (window.lucide?.createIcons) lucide.createIcons();
    
  };

  const fetchSaved = async () => {
    const res = await fetch("/marketplace/getSavedItems", { credentials: "same-origin" });
    const data = await res.json();
    if (!data.success) return;

    savedItems = Array.isArray(data.items) ? data.items : [];
    savedIds = new Set(savedItems.map((x) => normalizeItemId(x.id)).filter(Boolean));

    syncSaveButtons();
    renderSaved();
  };

  const toggleSave = async (itemId, forceUnsave = false) => {
    const normalizedItemId = normalizeItemId(itemId);
    const isSaved = savedIds.has(normalizedItemId);
    const shouldUnsave = forceUnsave || isSaved;

    const fd = new FormData();
    fd.append("item_id", normalizedItemId);

    const res = await fetch(shouldUnsave ? "/marketplace/unsaveItem" : "/marketplace/saveItem", {
      method: "POST",
      body: fd,
      credentials: "same-origin",
    });

    const data = await res.json();
    if (!data.success) {
      alert(data.message || "Failed");
      return;
    }

    await fetchSaved();
    if (savedSidebar) {
      setSidebarOpen(true);
    }
  };

  if (savedSidebar) {
    savedOpenBtn?.addEventListener("click", () => setSidebarOpen(true));
    savedCloseBtn?.addEventListener("click", () => setSidebarOpen(false));
    savedBackdrop?.addEventListener("click", () => setSidebarOpen(false));

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") setSidebarOpen(false);
    });
  }

  const saveButtons = document.querySelectorAll(".marketplace-save-btn");
  if (saveButtons.length) {
    saveButtons.forEach((btn) => {
      btn.addEventListener("click", async (e) => {
        e.preventDefault();
        e.stopPropagation();
        await toggleSave(btn.dataset.itemId);
      });
    });

    fetchSaved();
  }
});