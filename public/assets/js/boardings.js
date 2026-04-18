function openPostListingModal() {
  const modal = document.getElementById("post-listing-modal");
  modal.classList.add("active");
  document.body.style.overflow = "hidden";
}

let postListingSelectedFiles = [];



function closePostListingModal() {
  const modal = document.getElementById("post-listing-modal");
  modal.classList.remove("active");
  document.body.style.overflow = "auto";

  
  
  // Reset preview
  const previewContainer = document.getElementById("post-listing-image-preview");
  if (previewContainer) {
    previewContainer.innerHTML = "";
  }

  postListingSelectedFiles = [];
  
  // Reset form
  const form = document.getElementById("post-listing-form");
  if (form) form.reset();
  
  // Clear all error messages
  document.querySelectorAll('.error-message').forEach(msg => {
    msg.textContent = "";
    msg.classList.remove("show");
  });
}


function openPostListingImageSelector() {
  const photosInput = document.getElementById("post-listing-media-input");
  if (photosInput) {
    console.log("Opening image selector");
    photosInput.click();
  }
}






// Toggle filters visibility
function toggleFilters() {
  const filtersContainer = document.getElementById("filters-container");
  filtersContainer.classList.toggle("active");
}

// Apply filters and search
function applyFilters() {
  const searchQuery = document.getElementById("search-input").value.trim();
  const city = document.getElementById("filter-city").value.trim();
  const district = document.getElementById("filter-district").value.trim();
  const gender = document.getElementById("filter-gender").value;
  const category = document.getElementById("filter-category").value;
  const facilities = document.getElementById("filter-facilities").value;
  const minRent = document.getElementById("filter-min-rent").value;
  const maxRent = document.getElementById("filter-max-rent").value;

  const params = new URLSearchParams({
    search: searchQuery,
    city: city,
    district: district,
    gender: gender,
    category: category,
    facilities: facilities,
    min_rent: minRent,
    max_rent: maxRent
  });

  // Remove empty parameters
  for (let [key, value] of [...params.entries()]) {
    if (!value) {
      params.delete(key);
    }
  }

  fetch(`/boardings/searchListings?${params.toString()}`)
    .then(res => res.json())
    .then(data => {
      if (data && data.success) {
        displaySearchResults(data.rooms);
      } else {
        displaySearchResults([]); // forces "No boarding listings found matching your criteria."
        console.error("Search failed:", data?.message);
      }
    })
    .catch(err => {
      displaySearchResults([]); // also override server message on network/server error
      console.error("Search error:", err);
    });
}

// Display search results
function displaySearchResults(rooms) {
  const boardingItems = document.getElementById("boarding-items");
  
  if (!rooms || rooms.length === 0) {
    boardingItems.innerHTML = '<p class="no-listings">No boarding listings found matching your criteria.</p>';
    return;
  }

  boardingItems.innerHTML = '';
  
  rooms.forEach(room => {
    const locationText = room.city && room.district 
      ? `${room.city}, ${room.district}` 
      : 'Location not specified';

    const facilitiesText = room.facilities
      ? room.facilities
          .split(',')
          .map(f => f.trim())
          .filter(Boolean)
          .map(f => f.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' '))
          .join(', ')
      : 'WiFi';

    const facilitiesArray = room.facilities
      ? room.facilities
          .split(',')
          .map(f => f.trim())
          .filter(Boolean)
      : [];

    const facilitiesClass = room.facilities
      ? room.facilities.split(',')[0].trim().toLowerCase().replace(/\s+/g, '-')
      : 'wifi';

    const imageUrls = room.image_urls_csv
      ? room.image_urls_csv.split('|||').filter(Boolean)
      : (room.image_url ? [room.image_url] : []);

    const firstImageUrl = imageUrls.length > 0 ? imageUrls[0] : null;
    
    const card = document.createElement('div');
    card.className = 'boarding-card';
    card.setAttribute('data-room-id', room.id);
    
    card.innerHTML = `
      <div class="boarding-image">
        ${firstImageUrl
          ? `<img src="${firstImageUrl}" alt="Boarding Room" />`
          : '<div class="boarding-no-image">No Image Available</div>'}
      </div>
      <div class="boarding-details">
        <p class="boarding-rent">LKR ${parseFloat(room.rent).toLocaleString()}/month</p>
        <p class="boarding-occupancy">
          <i data-lucide="users"></i>
          ${room.occupancy} person${room.occupancy > 1 ? 's' : ''}
        </p>

        <span class="boarding-gender ${room.gender ? room.gender.toLowerCase() : 'male students'}">
          ${room.gender ? room.gender.charAt(0).toUpperCase() + room.gender.slice(1) : 'Male students'}
        </span>

        <p class="boarding-location">
          <i data-lucide="map-pin"></i>
          ${locationText}
        </p>

        <span class="boarding-category ${room.category ? room.category.toLowerCase() : 'single'}">
          ${room.category ? room.category.charAt(0).toUpperCase() + room.category.slice(1) : 'Single Room'}
        </span>
        ${facilitiesArray.length > 0
          ? `<div class="boarding-facilities-list">${facilitiesArray
              .map((facility) => `<span class="boarding-facility-chip">${facility
                .split(' ')
                .map(w => w.charAt(0).toUpperCase() + w.slice(1))
                .join(' ')}</span>`)
              .join('')}</div>`
          : ''}
        <span class="boarding-status ${room.status ? room.status.toLowerCase() : 'available'}">
          ${room.status ? room.status.charAt(0).toUpperCase() + room.status.slice(1) : 'Available'}
        </span>

        <p class="boarding-date">
          ${new Date(room.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
        </p>
       
      </div>
    `;
    
    boardingItems.appendChild(card);
  });
  
  // Re-initialize Lucide icons
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
}

// Clear all filters
function clearFilters() {
  document.getElementById("search-input").value = '';
  document.getElementById("filter-city").value = '';
  document.getElementById("filter-district").value = '';
  document.getElementById("filter-gender").value = '';
  document.getElementById("filter-category").value = '';
  document.getElementById("filter-facilities").value = '';
  document.getElementById("filter-min-rent").value = '';
  document.getElementById("filter-max-rent").value = '';
  
  // Reload all listings
  window.location.reload();
}

// Real-time search on input
function setupSearchInput() {
  const searchInput = document.getElementById("search-input");
  let searchTimeout;
  
  if (searchInput) {
    searchInput.addEventListener("input", function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        applyFilters();
      }, 500); // Debounce for 500ms
    });
  }
}




function showBoardingError(fieldId, message) {
  const field = document.getElementById(fieldId);
  let errorDiv;
  
  switch (fieldId) {
    case "room-rent":
      errorDiv = document.getElementById("post-listing-rent-error");
      break;
    case "room-occupancy":
      errorDiv = document.getElementById("post-listing-occupancy-error");
      break;
    case "room-gender":
      errorDiv = document.getElementById("post-listing-gender-error");
      break;
    case "room-city":
      errorDiv = document.getElementById("post-listing-city-error");
      break;
    case "room-district":
      errorDiv = document.getElementById("post-listing-district-error");
      break;
    case "room-status":
      errorDiv = document.getElementById("post-listing-status-error");
      break;
    case "room-category":
      errorDiv = document.getElementById("post-listing-category-error");
      break;
    case "post-listing-media-input":
      errorDiv = document.getElementById("post-listing-media-error");
      break;
    case "room-facilities":
      errorDiv = document.getElementById("post-listing-facilities-error");
      break;
    case "room-contact-number":
      errorDiv = document.getElementById("post-listing-contact-number-error");
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


function showBoardingSuccess(fieldId) {
  const field = document.getElementById(fieldId);
  let errorDiv;
  
  switch (fieldId) {
    case "room-rent":
      errorDiv = document.getElementById("post-listing-rent-error");
      break;
    case "room-occupancy":
      errorDiv = document.getElementById("post-listing-occupancy-error");
      break;
    case "room-gender":
      errorDiv = document.getElementById("post-listing-gender-error");
      break;
    case "room-city":
      errorDiv = document.getElementById("post-listing-city-error");
      break;
    case "room-district":
      errorDiv = document.getElementById("post-listing-district-error");
      break;
    case "room-status":
      errorDiv = document.getElementById("post-listing-status-error");
      break;
    case "room-category":
      errorDiv = document.getElementById("post-listing-category-error");
      break;
    case "post-listing-media-input":
      errorDiv = document.getElementById("post-listing-media-error");
      break;
    case "room-facilities":
      errorDiv = document.getElementById("post-listing-facilities-error");
      break;
    case "room-contact-number":
      errorDiv = document.getElementById("post-listing-contact-number-error");
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

function validatePostListing() {
  const rent = document.getElementById("room-rent").value.trim();
  const occupancy = document.getElementById("room-occupancy").value.trim();
  const gender = document.getElementById("room-gender").value;
  const city = document.getElementById("room-city").value.trim();
  const district = document.getElementById("room-district").value.trim();
  const status = document.getElementById("room-status").value;
  const category = document.getElementById("room-category").value;
  const mediaFiles = document.getElementById("post-listing-media-input").files;
  const facilitiesCheckboxes = document.querySelectorAll('input[name="facilities[]"]');
  let selectedFacilities = 0;
  if (facilitiesCheckboxes.length > 0) {
    selectedFacilities = Array.from(facilitiesCheckboxes).filter(cb => cb.checked).length;
  } else {
    const facilitiesSelect = document.getElementById("room-facilities");
    selectedFacilities = facilitiesSelect ? Array.from(facilitiesSelect.selectedOptions || []).length : 0;
  }
  let valid = true;
  const contactNumber = document.getElementById("room-contact-number").value.trim();

  
  // Rent validation
  if (!rent) {
    showBoardingError("room-rent", "Monthly rent is required");
    valid = false;
  } else if (isNaN(rent) || parseFloat(rent) <= 0) {
    showBoardingError("room-rent", "Please enter a valid rent amount");
    valid = false;
  } else {
    showBoardingSuccess("room-rent");
  }

  // Occupancy validation
  if (!occupancy) {
    showBoardingError("room-occupancy", "Occupancy is required");
    valid = false;
  } else if (isNaN(occupancy) || parseInt(occupancy) <= 0 || parseInt(occupancy) > 10) {
    showBoardingError("room-occupancy", "Please enter a number between 1-10");
    valid = false;
  } else {
    showBoardingSuccess("room-occupancy");
  }
  
  // Gender validation
  if (!gender) {
    showBoardingError("room-gender", "Preferred gender is required");
    valid = false;
  } else {
    showBoardingSuccess("room-gender");
  }
  // City validation
  if (!city) {
    showBoardingError("room-city", "City is required");
    valid = false;
  } else {
    showBoardingSuccess("room-city");
  }

  // District validation
  if (!district) {
    showBoardingError("room-district", "District is required");
    valid = false;
  } else {
    showBoardingSuccess("room-district");
  }

  // Status validation
  if (!status) {
    showBoardingError("room-status", "Status is required");
    valid = false;
  } else {
    showBoardingSuccess("room-status");
  }

  // Category validation
  if (!category) {
    showBoardingError("room-category", "Category is required");
    valid = false;
  } else {
    showBoardingSuccess("room-category");
  }

  // Image validation (optional - remove if image is not required)
  if (!mediaFiles || mediaFiles.length === 0) {
    showBoardingError("post-listing-media-input", "At least one image is required");
    valid = false;
  } else if (mediaFiles.length > 5) {
    showBoardingError("post-listing-media-input", "You can upload maximum 5 images");
    valid = false;
  } else {
    showBoardingSuccess("post-listing-media-input");
  }

  // Facilities validation disabled: always keep this field in non-error state.
  showBoardingSuccess("room-facilities");

  // Contact number validation
  const contact = document.getElementById("room-contact-number").value.trim();

  if (!contact) {
    showBoardingError("room-contact-number", "Contact number is required");
    valid = false;
  } else if (!/^[0-9+\-\s]{7,20}$/.test(contact)) {
    showBoardingError("room-contact-number", "Please enter a valid contact number");
    valid = false;
  } else {
    showBoardingSuccess("room-contact-number");
  }

  console.log("Validation result:", valid);
  return valid;
}

function setupBoardingLiveValidation(fieldIds) {
  function validateFieldById(id) {
    switch (id) {
      case "room-rent": {
        const rentInput = document.getElementById("room-rent");
        const rent = rentInput ? rentInput.value.trim() : "";
        if (!rent) {
          showBoardingError("room-rent", "Monthly rent is required");
          return false;
        }
        if (isNaN(rent) || parseFloat(rent) <= 0) {
          showBoardingError("room-rent", "Please enter a valid rent amount");
          return false;
        }
        showBoardingSuccess("room-rent");
        return true;
      }
      case "room-occupancy": {
        const occupancyInput = document.getElementById("room-occupancy");
        const occupancy = occupancyInput ? occupancyInput.value.trim() : "";
        if (!occupancy) {
          showBoardingError("room-occupancy", "Occupancy is required");
          return false;
        }
        if (isNaN(occupancy) || parseInt(occupancy, 10) <= 0 || parseInt(occupancy, 10) > 10) {
          showBoardingError("room-occupancy", "Please enter a number between 1-10");
          return false;
        }
        showBoardingSuccess("room-occupancy");
        return true;
      }
      case "room-gender": {
        const genderInput = document.getElementById("room-gender");
        const gender = genderInput ? genderInput.value : "";
        if (!gender) {
          showBoardingError("room-gender", "Preferred gender is required");
          return false;
        }
        showBoardingSuccess("room-gender");
        return true;
      }
      case "room-city": {
        const cityInput = document.getElementById("room-city");
        const city = cityInput ? cityInput.value.trim() : "";
        if (!city) {
          showBoardingError("room-city", "City is required");
          return false;
        }
        showBoardingSuccess("room-city");
        return true;
      }
      case "room-district": {
        const districtInput = document.getElementById("room-district");
        const district = districtInput ? districtInput.value.trim() : "";
        if (!district) {
          showBoardingError("room-district", "District is required");
          return false;
        }
        showBoardingSuccess("room-district");
        return true;
      }
      case "room-status": {
        const statusInput = document.getElementById("room-status");
        const status = statusInput ? statusInput.value : "";
        if (!status) {
          showBoardingError("room-status", "Status is required");
          return false;
        }
        showBoardingSuccess("room-status");
        return true;
      }
      case "room-category": {
        const categoryInput = document.getElementById("room-category");
        const category = categoryInput ? categoryInput.value : "";
        if (!category) {
          showBoardingError("room-category", "Category is required");
          return false;
        }
        showBoardingSuccess("room-category");
        return true;
      }
      case "post-listing-media-input": {
        const mediaInput = document.getElementById("post-listing-media-input");
        const mediaFiles = mediaInput ? mediaInput.files : null;
        if (!mediaFiles || mediaFiles.length === 0) {
          showBoardingError("post-listing-media-input", "At least one image is required");
          return false;
        }
        if (mediaFiles.length > 5) {
          showBoardingError("post-listing-media-input", "You can upload maximum 5 images");
          return false;
        }
        showBoardingSuccess("post-listing-media-input");
        return true;
      }
      case "room-facilities":
        showBoardingSuccess("room-facilities");
        return true;
      case "room-contact-number": {
        const contactInput = document.getElementById("room-contact-number");
        const contact = contactInput ? contactInput.value.trim() : "";
        if (!contact) {
          showBoardingError("room-contact-number", "Contact number is required");
          return false;
        }
        if (!/^[0-9+\-\s]{7,20}$/.test(contact)) {
          showBoardingError("room-contact-number", "Please enter a valid contact number");
          return false;
        }
        showBoardingSuccess("room-contact-number");
        return true;
      }
      default:
        return true;
    }
  }

  fieldIds.forEach((id) => {
    const input = document.getElementById(id);
    if (!input) return;
    
    input.addEventListener("input", () => {
      validateFieldById(id);
    });

    input.addEventListener("change", () => {
      validateFieldById(id);
    });

    input.addEventListener("blur", () => {
      validateFieldById(id);
    });
  });

  // For file input
  const mediaInput = document.getElementById("post-listing-media-input");
  if (mediaInput) {
    mediaInput.addEventListener("change", () => {
      validateFieldById("post-listing-media-input");
    });
  }

  // For facilities checkboxes
  const facilitiesCheckboxes = document.querySelectorAll('input[name="facilities[]"]');
  facilitiesCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      validateFieldById("room-facilities");
    });
  });
}

// Image preview handler
function setupImagePreview() {
  const mediaInput = document.getElementById("post-listing-media-input");
  const previewContainer = document.getElementById("post-listing-image-preview");
  
  if (mediaInput && previewContainer) {
    const syncInputFiles = () => {
      const dt = new DataTransfer();
      postListingSelectedFiles.forEach((file) => dt.items.add(file));
      mediaInput.files = dt.files;
    };

    const renderPreview = () => {
      previewContainer.innerHTML = "";
      postListingSelectedFiles.forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = document.createElement("img");
          img.src = e.target.result;
          img.alt = "Boarding image preview";
          img.className = "post-listing-preview-item";
          previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    };

    mediaInput.addEventListener("change", function () {
      const newFiles = Array.from(mediaInput.files || []);
      newFiles.forEach((file) => {
        const exists = postListingSelectedFiles.some(
          (selected) =>
            selected.name === file.name &&
            selected.size === file.size &&
            selected.lastModified === file.lastModified
        );
        if (!exists) {
          postListingSelectedFiles.push(file);
        }
      });

      if (postListingSelectedFiles.length > 5) {
        postListingSelectedFiles = postListingSelectedFiles.slice(0, 5);
        showBoardingError("post-listing-media-input", "You can upload maximum 5 images");
      } else {
        showBoardingSuccess("post-listing-media-input");
      }

      syncInputFiles();
      renderPreview();
    });
  }
}

// Facilities section toggle
function setupFacilitiesToggle() {
  const categorySelect = document.getElementById("room-category");
  const facilitiesSection = document.getElementById("facilities-section");

  if (categorySelect && facilitiesSection) {
    categorySelect.addEventListener("change", function () {
      if (this.value !== "") {
        facilitiesSection.style.display = "block";
      } else {
        facilitiesSection.style.display = "none";
      }
    });

    // Initialize visibility based on current value (useful if re-opening modal)
    if (categorySelect.value !== "") {
      facilitiesSection.style.display = "block";
    } else {
      facilitiesSection.style.display = "none";
    }
  }
}

function setupFacilitiesMultiSelect() {
  const facilitiesContainer = document.getElementById("room-facilities");
  const facilitiesToggle = document.getElementById("room-facilities-toggle");
  const facilitiesMenu = document.getElementById("room-facilities-menu");

  if (!facilitiesContainer || !facilitiesToggle || !facilitiesMenu) {
    return;
  }

  const facilityCheckboxes = facilitiesMenu.querySelectorAll('input[name="facilities[]"]');

  const updateFacilitiesToggleText = () => {
    const selectedLabels = Array.from(facilityCheckboxes)
      .filter((checkbox) => checkbox.checked)
      .map((checkbox) => {
        const label = checkbox.closest("label");
        return label ? label.textContent.trim() : "";
      })
      .filter(Boolean);

    facilitiesToggle.textContent = selectedLabels.length > 0
      ? selectedLabels.join(", ")
      : "Select Available Facilities";
  };

  facilitiesToggle.addEventListener("click", (event) => {
    event.stopPropagation();
    facilitiesMenu.classList.toggle("active");
  });

  facilitiesMenu.addEventListener("click", (event) => {
    event.stopPropagation();
  });

  facilityCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", updateFacilitiesToggleText);
  });

  document.addEventListener("click", () => {
    facilitiesMenu.classList.remove("active");
  });

  updateFacilitiesToggleText();
}





// Main initialization
document.addEventListener("DOMContentLoaded", function () {
  console.log("Boardings JS initialized");

  // Works on both boardings modal page and edit listing page.
  setupFacilitiesMultiSelect();

  setupSearchInput();

  // Always setup location selects (works on both pages)
  setupPostListingLocationSelects();
  setupEditListingLocationSelects();

  // Only the post-listing modal has this form
  const form = document.getElementById("post-listing-form");
  if (!form) {
    console.log("post-listing-form not found (ok on edit page)");
    return;
  }

  console.log("Form found, setting up handlers");

  // Setup live validation
  setupBoardingLiveValidation([
    "room-rent",
    "room-occupancy",
    "room-gender",
    "room-city",
    "room-district",
    "room-status",
    "room-category",
    "room-facilities",
    "post-listing-media-input",
    "room-contact-number",
  ]);

  // Setup image preview
  setupImagePreview();

  // Setup facilities toggle
  setupFacilitiesToggle();

  setupPostListingLocationSelects();
  setupEditListingLocationSelects();

  // Form submission handler
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    console.log("Form submitted!");

    // Validate form
    if (!validatePostListing()) {
      console.log("Validation failed");
      return false;
    }

    console.log("Validation passed, submitting...");

    const spinner = document.getElementById("post-listing-loading-spinner");
    if (spinner) spinner.style.display = "flex";

   

    const formData = new FormData(form);



    // Log form data for debugging
    for (let pair of formData.entries()) {
      console.log(pair[0] + ': ' + pair[1]);
    }

    fetch("/boardings/createListing", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((res) => {
        console.log("Response status:", res.status);
        return res.text().then((text) => {
          let data = null;
          try {
            data = text ? JSON.parse(text) : null;
          } catch (parseError) {
            throw new Error(text || `HTTP error! status: ${res.status}`);
          }

          if (!res.ok || !data) {
            throw new Error((data && data.message) || `HTTP error! status: ${res.status}`);
          }

          return data;
        });
      })
      .then((data) => {
        console.log("Response data:", data);
        if (data.success) {
          console.log("Listing created successfully!");
          closePostListingModal();
          window.location.reload();
        } else {
          alert("Error: " + (data.message || "Unknown error"));
        }
      })
      .catch((err) => {
        console.error("Fetch error:", err);
        alert("Error creating listing: " + err.message);
      })
      .finally(() => {
        if (spinner) spinner.style.display = "none";
      });
  });

  console.log("Form submit handler attached");
});

const DISTRICT_TO_CITIES = {
  Colombo: ["Colombo", "Dehiwala", "Moratuwa", "Kotte", "Maharagama"],
  Gampaha: ["Gampaha", "Negombo", "Wattala", "Ja-Ela", "Kelaniya"],
  Kalutara: ["Kalutara", "Panadura", "Beruwala", "Horana"],
  Kandy: ["Kandy", "Peradeniya", "Katugastota", "Gampola"],
  Galle: ["Galle", "Hikkaduwa", "Ambalangoda", "Elpitiya"],
  Matara: ["Matara", "Weligama", "Dikwella"],
  Kurunegala: ["Kurunegala", "Kuliyapitiya", "Narammala"],
  Anuradhapura: ["Anuradhapura", "Kekirawa", "Mihintale"],
  Jaffna: ["Jaffna", "Chavakachcheri", "Nallur"],
  Batticaloa: ["Batticaloa", "Kattankudy", "Eravur"],
  Trincomalee: ["Trincomalee", "Kinniya", "Mutur"],
  Badulla: ["Badulla", "Bandarawela", "Ella"],
  Ratnapura: ["Ratnapura", "Balangoda", "Embilipitiya"],
  Puttalam: ["Puttalam", "Chilaw", "Wennappuwa"],
  Hambantota: ["Hambantota", "Tangalle", "Tissamaharama"],
  Monaragala: ["Monaragala", "Wellawaya", "Bibile"],
  Matale: ["Matale", "Dambulla", "Galewela"],
  Polonnaruwa: ["Polonnaruwa", "Hingurakgoda", "Kaduruwela"],
  Ampara: ["Ampara", "Kalmunai", "Akkaraipattu"],
  Kegalle: ["Kegalle", "Mawanella", "Rambukkana"],
  Mannar: ["Mannar"],
  Vavuniya: ["Vavuniya"],
  Kilinochchi: ["Kilinochchi"],
  Mullaitivu: ["Mullaitivu"],
  "Nuwara Eliya": ["Nuwara Eliya", "Hatton", "Talawakele"],
};

function setupPostListingLocationSelects() {
  const districtSelect = document.getElementById("room-district");
  const citySelect = document.getElementById("room-city");
  if (!districtSelect || !citySelect) return;

  const resetCities = () => {
    citySelect.innerHTML = `<option value="" disabled selected>Select City</option>`;
    citySelect.disabled = true;
  };

  const populateCities = (district) => {
    const cities = DISTRICT_TO_CITIES[district] || [];
    citySelect.innerHTML = `<option value="" disabled selected>Select City</option>`;
    cities.forEach((c) => {
      const opt = document.createElement("option");
      opt.value = c;
      opt.textContent = c;
      citySelect.appendChild(opt);
    });
    citySelect.disabled = cities.length === 0;
  };

  resetCities();

  districtSelect.addEventListener("change", () => {
    populateCities(districtSelect.value);
    citySelect.classList.remove("error", "success");
  });
  
}

function setupEditListingLocationSelects() {
  const districtSelect = document.getElementById("district") || document.getElementById("room-district");
const citySelect = document.getElementById("city") || document.getElementById("room-city");
  if (!districtSelect || !citySelect) return;

  const savedCity = citySelect.dataset.savedCity || "";

  const resetCities = () => {
    citySelect.innerHTML = `<option value="" disabled selected>Select City</option>`;
    citySelect.disabled = true;
  };

  const populateCities = (district, cityToSelect = "") => {
    const cities = DISTRICT_TO_CITIES[district] || [];
    citySelect.innerHTML = `<option value="" disabled selected>Select City</option>`;

    cities.forEach((c) => {
      const opt = document.createElement("option");
      opt.value = c;
      opt.textContent = c;
      citySelect.appendChild(opt);
    });

    citySelect.disabled = cities.length === 0;

    if (cityToSelect) {
      const hasOption = Array.from(citySelect.options).some(o => o.value === cityToSelect);
      if (!hasOption) {
        const opt = document.createElement("option");
        opt.value = cityToSelect;
        opt.textContent = cityToSelect;
        citySelect.appendChild(opt);
        citySelect.disabled = false;
      }
      citySelect.value = cityToSelect;
    }
  };

  resetCities();

  if (districtSelect.value) {
    populateCities(districtSelect.value, savedCity);
  }

  districtSelect.addEventListener("change", () => {
    populateCities(districtSelect.value, "");
    citySelect.classList.remove("error", "success");
  });
}


