function toggleMyListingsOptions(id) {
  console.log("Toggling my listings options for ID:", id);
  const listingOptions = document.getElementById(
    `my-listings-options-dropdown-${id}`
  );

  console.log("Found element:", listingOptions);
  console.log(
    "Current opacity:",
    listingOptions ? listingOptions.style.opacity : "not found"
  );

  if (!listingOptions) {
    console.error("Dropdown not found for ID:", id);
    return;
  }

  // Close all other dropdowns first
  const allDropdowns = document.querySelectorAll(".my-listings-options-dropdown");
  allDropdowns.forEach((dropdown) => {
    if (dropdown.id !== `my-listings-options-dropdown-${id}`) {
      dropdown.style.opacity = "0";
      dropdown.style.zIndex = "-1";
      dropdown.style.pointerEvents = "none";
    }
  });

  if (listingOptions.style.opacity === "1") {
    closeMyListingsOptions(id);
  } else {
    openMyListingsOptions(id);
  }
}

function openMyListingsOptions(id) {
  const listingOptions = document.getElementById(
    `my-listings-options-dropdown-${id}`
  );

  if (!listingOptions) {
    console.error("Cannot open - dropdown not found");
    return;
  }

  listingOptions.style.display = "block";
  listingOptions.style.zIndex = "10";
  listingOptions.style.pointerEvents = "auto";

  gsap.to(listingOptions, {
    duration: 0.3,
    opacity: 1,
    ease: "power2.out",
  });
}

function closeMyListingsOptions(id) {
  const listingOptions = document.getElementById(
    `my-listings-options-dropdown-${id}`
  );

  if (!listingOptions) return;

  gsap.to(listingOptions, {
    duration: 0.3,
    opacity: 0,
    ease: "power2.out",
    onComplete: () => {
      listingOptions.style.zIndex = "-1";
      listingOptions.style.pointerEvents = "none";
      listingOptions.style.display = "none";
    },
  });
}

// Delete listing function
function deleteListing(btn, event) {
  event.stopPropagation();
  const roomId = btn.getAttribute("data-room-id");

  console.log("Attempting to delete listing:", roomId);

  if (confirm("Are you sure you want to delete this boarding listing?")) {
    console.log("Confirmed delete, sending request...");

    const formData = new FormData();
    formData.append("delete_room_id", roomId);

    fetch(`/boardings/deleteListing`, {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((res) => {
        console.log("Response status:", res.status);
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          console.log("Listing deleted successfully, reloading...");
          window.location.reload();
        } else {
          alert("Error: " + (data.message || "Failed to delete listing"));
        }
      })
      .catch((err) => {
        console.error("Delete error:", err);
        alert("Error deleting listing: " + err.message);
      });
  } else {
    console.log("Delete cancelled");
  }
}

//Edit listing function
//function editListing(btn, event) {
  //event.stopPropagation();
  //const roomId = btn.getAttribute("data-room-id");
  
  //console.log("Edit listing:", roomId);
  
  // Redirect to edit page or open edit modal
  // Option 1: Redirect to edit page
  //window.location.href = `/boardings/editListing/${roomId}`;
  
  // Option 2: Open edit modal (if you create one)
  // openEditListingModal(roomId);
//}


// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
  const dropdowns = document.querySelectorAll(".my-listings-options-dropdown");
  const optionsButtons = document.querySelectorAll(".my-listings-options");

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
  console.log("Boarding card JS loaded");

  const shouldIgnoreCardClick = (target) => {
    return !!target.closest(
      ".my-listings-options, .my-listings-options-dropdown, .edit-listing-btn, .delete-listing-btn, a, button, input, select, textarea, label"
    );
  };

  document.addEventListener("click", function (event) {
    const card = event.target.closest(".boarding-card");
    if (!card || shouldIgnoreCardClick(event.target)) {
      return;
    }

    const roomId = card.getAttribute("data-room-id");
    if (roomId) {
      window.location.href = `/boardings/details/${roomId}`;
    }
  });

  const deleteButtons = document.querySelectorAll(".delete-listing-btn");
  console.log("Found delete buttons:", deleteButtons.length);

  deleteButtons.forEach((btn) => {
    console.log(
      "Attaching delete listener to button with ID:",
      btn.getAttribute("data-room-id")
    );
    btn.addEventListener("click", function (e) {
      console.log("Delete button clicked!");
      deleteListing(this, e);
    });
  });

  const editButtons = document.querySelectorAll(".edit-listing-btn");
  console.log("Found edit buttons:", editButtons.length);

  editButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const roomId = this.dataset.roomId;
      console.log("Edit button clicked!", roomId);
      window.location.href = `/boardings/editListing/${roomId}`;
    });
  });
});