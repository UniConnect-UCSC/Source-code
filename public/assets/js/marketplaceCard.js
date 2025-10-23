function toggleMyItemsOptions(id) {
  const itemOptions = document.getElementById(
    `my-items-options-dropdown-${id}`
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
function deleteMarketplaceItem(btn, event) {
  event.stopPropagation();
  const itemId = btn.getAttribute("data-item-id");

  if (confirm("Are you sure you want to delete this item?")) {
    const formData = new FormData();
    formData.append("delete_item_id", itemId);

    fetch("/marketplace/deleteItem", {
      method: "POST",
      body: formData,
    })
      .then(async (response) => {
        const text = await response.text();
        console.log("Raw response:", text);

        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("Failed to parse JSON:", e);
          console.error("Response text was:", text);
          throw new Error("Invalid JSON response from server");
        }
      })
      .then((data) => {
        console.log("Parsed data:", data);
        if (data.success) {
          window.location.reload();
        } else {
          alert("Failed to delete: " + (data.message || "Unknown error"));
        }
      })
      .catch((error) => {
        console.error("Delete error:", error);
        alert("An error occurred while deleting the item");
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
