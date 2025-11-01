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

    fetch(`/marketplace/deleteItem/${itemId}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((res) => {
        console.log("Response status:", res.status);
        if (!res.ok) throw new Error("Failed to delete item");
        console.log("Item deleted successfully, reloading...");
        window.location.reload();
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
      // TODO: Implement edit functionality
    });
  });
});
