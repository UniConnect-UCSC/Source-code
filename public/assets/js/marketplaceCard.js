function toggleMyItemsOptions(id) {
  console.log("Toggling my items options for ID:", id);
  const itemOptions = document.getElementById(
    `my-items-options-dropdown-${id}`
  );

  console.log("Found element:", itemOptions);
  console.log(
    "Current opacity:",
    itemOptions ? itemOptions.style.opacity : "not found"
  );

  if (!itemOptions) {
    console.error("Dropdown not found for ID:", id);
    return;
  }

  // Close all other dropdowns first
  const allDropdowns = document.querySelectorAll(".my-items-options-dropdown");
  allDropdowns.forEach((dropdown) => {
    if (dropdown.id !== `my-items-options-dropdown-${id}`) {
      dropdown.style.opacity = "0";
      dropdown.style.zIndex = "-1";
      dropdown.style.pointerEvents = "none";
    }
  });

  if (itemOptions.style.opacity === "1") {
    closeMyItemsOptions(id);
  } else {
    openMyItemsOptions(id);
  }
}

function openMyItemsOptions(id) {
  const itemOptions = document.getElementById(
    `my-items-options-dropdown-${id}`
  );

  if (!itemOptions) {
    console.error("Cannot open - dropdown not found");
    return;
  }

  itemOptions.style.display = "block";
  itemOptions.style.zIndex = "10";
  itemOptions.style.pointerEvents = "auto";

  gsap.to(itemOptions, {
    duration: 0.3,
    opacity: 1,
    ease: "power2.out",
  });
}

function closeMyItemsOptions(id) {
  const itemOptions = document.getElementById(
    `my-items-options-dropdown-${id}`
  );

  if (!itemOptions) return;

  gsap.to(itemOptions, {
    duration: 0.3,
    opacity: 0,
    ease: "power2.out",
    onComplete: () => {
      itemOptions.style.zIndex = "-1";
      itemOptions.style.pointerEvents = "none";
      itemOptions.style.display = "none";
    },
  });
}

// Delete item function
function deleteItem(btn, event) {
  event.stopPropagation();
  const itemId = btn.getAttribute("data-item-id");

  console.log("Attempting to delete item:", itemId);

  if (confirm("Are you sure you want to delete this item?")) {
    console.log("Confirmed delete, sending request...");

    const formData = new FormData();
    formData.append("delete_item_id", itemId);

    fetch(`/marketplace/deleteItem`, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) window.location.reload();
        else alert("Error: " + (data.message || "Failed to delete item"));
      })
      .catch((err) => {
        console.error("Delete error:", err);
        alert("Error deleting item: " + err.message);
      });
  } else {
    console.log("Delete cancelled");
  }
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
  const dropdowns = document.querySelectorAll(".my-items-options-dropdown");
  const optionsButtons = document.querySelectorAll(".my-items-options");

  let clickedInside = false;

  optionsButtons.forEach((button) => {
    if (button.contains(event.target)) {
      clickedInside = true;
    }
  });

  dropdowns.forEach((dropdown) => {
    if (dropdown.contains(event.target)) {
      clickedInside = true;
    }
  });

  if (!clickedInside) {
    dropdowns.forEach((dropdown) => {
      dropdown.style.opacity = "0";
      dropdown.style.zIndex = "-1";
      dropdown.style.pointerEvents = "none";
      dropdown.style.display = "none";
    });
  }
});

// Attach event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  console.log("Marketplace card JS loaded");

  const interactiveSelector =
    "a, button, input, textarea, select, label, .my-items-options, .my-items-options-dropdown, .edit-item-btn, .delete-item-btn, .marketplace-save-btn, .marketplace-contact-seller";

  const marketplaceCards = document.querySelectorAll(".marketplace-item-card");
  marketplaceCards.forEach((card) => {
    const detailsUrl = card.dataset.detailsUrl || `/marketplace/details/${card.dataset.itemId}`;

    const navigateToDetails = () => {
      if (!detailsUrl) return;
      window.location.href = detailsUrl;
    };

    card.addEventListener("click", function (e) {
      if (e.target.closest(interactiveSelector)) {
        return;
      }
      navigateToDetails();
    });

    card.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        if (e.target.closest(interactiveSelector)) {
          return;
        }
        e.preventDefault();
        navigateToDetails();
      }
    });
  });

  const deleteButtons = document.querySelectorAll(".delete-item-btn");
  console.log("Found delete buttons:", deleteButtons.length);

  deleteButtons.forEach((btn) => {
    console.log(
      "Attaching delete listener to button with ID:",
      btn.getAttribute("data-item-id")
    );
    btn.addEventListener("click", function (e) {
      console.log("Delete button clicked!");
      deleteItem(this, e);
    });
  });

  const editButtons = document.querySelectorAll(".edit-item-btn");
  console.log("Found edit buttons:", editButtons.length);

  editButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const itemId = this.dataset.itemId;
      console.log("Edit item:", itemId);
      window.location.href = `/marketplace/editItem/${itemId}`;
      // TODO: Implement edit functionality
    });
  });

  
});
