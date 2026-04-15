// Remove the ID from the URL after loading the edit form
document.addEventListener('DOMContentLoaded', function () {
  var path = window.location.pathname;
  var match = path.match(/^(\/boardings\/editListing)(?:\/[\w-]+)?$/);
  if (match && match[1]) {
    var newUrl = match[1];
    if (window.location.pathname !== newUrl) {
      window.history.replaceState({}, document.title, newUrl);
    }
  }

  var removeButtons = document.querySelectorAll('.remove-current-image-btn');
  var removeIdsContainer = document.getElementById('remove-image-ids-container');
  var newImagesInput = document.getElementById('image');
  var selectedNewFiles = [];
  var rentInput = document.getElementById('rent');
  var occupancyInput = document.getElementById('occupancy');
  var districtInput = document.getElementById('district');
  var cityInput = document.getElementById('city');
  var categoryInput = document.getElementById('category');
  var genderInput = document.getElementById('gender');
  var statusInput = document.getElementById('status');
  var contactInput = document.getElementById('contact_number');

  function showFieldError(input, errorElementId, message) {
    var errorElement = document.getElementById(errorElementId);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.add('show');
    }

    if (input) {
      input.classList.add('error');
      input.classList.remove('success');
    }

    return false;
  }

  function showFieldSuccess(input, errorElementId) {
    var errorElement = document.getElementById(errorElementId);
    if (errorElement) {
      errorElement.textContent = '';
      errorElement.classList.remove('show');
    }

    if (input) {
      input.classList.remove('error');
      input.classList.add('success');
    }

    return true;
  }

  function normalizeContactNumber(value) {
    return String(value || '').replace(/\D+/g, '');
  }

  function validateRent() {
    var rent = rentInput ? rentInput.value.trim() : '';
    if (!rent) {
      return showFieldError(rentInput, 'edit-listing-rent-error', 'Monthly rent is required');
    }

    if (isNaN(rent) || parseFloat(rent) <= 0) {
      return showFieldError(rentInput, 'edit-listing-rent-error', 'Please enter a valid rent amount');
    }

    return showFieldSuccess(rentInput, 'edit-listing-rent-error');
  }

  function validateOccupancy() {
    var occupancy = occupancyInput ? occupancyInput.value.trim() : '';
    if (!occupancy) {
      return showFieldError(occupancyInput, 'edit-listing-occupancy-error', 'Occupancy is required');
    }

    if (isNaN(occupancy) || parseInt(occupancy, 10) <= 0 || parseInt(occupancy, 10) > 10) {
      return showFieldError(occupancyInput, 'edit-listing-occupancy-error', 'Please enter a number between 1-10');
    }

    return showFieldSuccess(occupancyInput, 'edit-listing-occupancy-error');
  }

  function validateSelectField(input, errorElementId, message) {
    if (!input || !input.value) {
      return showFieldError(input, errorElementId, message);
    }

    return showFieldSuccess(input, errorElementId);
  }

  function validateContactNumber() {
    if (!contactInput) return true;

    var contact = contactInput.value.trim();
    if (!contact) {
      return showFieldError(contactInput, 'edit-listing-contact-number-error', 'Contact number is required');
    }

    if (!/^[0-9+\-\s]{7,20}$/.test(contact)) {
      return showFieldError(contactInput, 'edit-listing-contact-number-error', 'Please enter a valid contact number');
    }

    return showFieldSuccess(contactInput, 'edit-listing-contact-number-error');
  }

  function validateImageCount() {
    var remainingCurrentImages = document.querySelectorAll('.current-image-item').length;
    var newImageCount = (newImagesInput && newImagesInput.files) ? newImagesInput.files.length : 0;
    var totalImageCount = remainingCurrentImages + newImageCount;

    if (totalImageCount < 1) {
      return showFieldError(newImagesInput, 'edit-listing-image-error', 'At least one image is required');
    }

    if (totalImageCount > 5) {
      return showFieldError(newImagesInput, 'edit-listing-image-error', 'You can upload maximum 5 images');
    }

    return showFieldSuccess(newImagesInput, 'edit-listing-image-error');
  }

  function validateFacilities() {
    var facilitiesContainer = document.getElementById('room-facilities');
    return showFieldSuccess(facilitiesContainer, 'edit-listing-facilities-error');
  }

  removeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var imageId = this.getAttribute('data-image-id');
      if (!imageId || !removeIdsContainer) return;

      var hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = 'remove_image_ids[]';
      hiddenInput.value = imageId;
      removeIdsContainer.appendChild(hiddenInput);

      var imageItem = this.closest('.current-image-item');
      if (imageItem) {
        imageItem.remove();
      }

      validateImageCount();
    });
  });

  function syncNewFilesInput() {
    if (!newImagesInput) return;
    var dt = new DataTransfer();
    selectedNewFiles.forEach(function (file) {
      dt.items.add(file);
    });
    newImagesInput.files = dt.files;
  }

  if (newImagesInput) {
    newImagesInput.addEventListener('change', function () {
      var exceededLimit = false;
      var pickedFiles = Array.from(newImagesInput.files || []);
      pickedFiles.forEach(function (file) {
        var exists = selectedNewFiles.some(function (selectedFile) {
          return selectedFile.name === file.name
            && selectedFile.size === file.size
            && selectedFile.lastModified === file.lastModified;
        });

        if (!exists) {
          selectedNewFiles.push(file);
        }
      });

      var remainingCurrentImages = document.querySelectorAll('.current-image-item').length;
      var maxNewImages = Math.max(0, 5 - remainingCurrentImages);
      if (selectedNewFiles.length > maxNewImages) {
        exceededLimit = true;
        selectedNewFiles = selectedNewFiles.slice(0, maxNewImages);
      }

      syncNewFilesInput();
      if (exceededLimit) {
        showFieldError(newImagesInput, 'edit-listing-image-error', 'You can upload maximum 5 images');
      } else {
        validateImageCount();
      }
    });
  }

  var editForm = document.querySelector('.edit-listing-form');

  if (rentInput) {
    rentInput.addEventListener('input', validateRent);
  }

  if (occupancyInput) {
    occupancyInput.addEventListener('input', validateOccupancy);
  }

  if (districtInput) {
    districtInput.addEventListener('change', function () {
      validateSelectField(districtInput, 'edit-listing-district-error', 'District is required');
    });
  }

  if (cityInput) {
    cityInput.addEventListener('change', function () {
      validateSelectField(cityInput, 'edit-listing-city-error', 'City is required');
    });
  }

  if (categoryInput) {
    categoryInput.addEventListener('change', function () {
      validateSelectField(categoryInput, 'edit-listing-category-error', 'Category is required');
    });
  }

  if (genderInput) {
    genderInput.addEventListener('change', function () {
      validateSelectField(genderInput, 'edit-listing-gender-error', 'Preferred gender is required');
    });
  }

  if (statusInput) {
    statusInput.addEventListener('change', function () {
      validateSelectField(statusInput, 'edit-listing-status-error', 'Status is required');
    });
  }

  if (contactInput) {
    contactInput.addEventListener('input', validateContactNumber);
  }

  var facilitiesCheckboxes = document.querySelectorAll('input[name="facilities[]"]');
  facilitiesCheckboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', validateFacilities);
  });

  if (editForm) {
    editForm.addEventListener('submit', function (event) {
      var isValid = true;

      isValid = validateRent() && isValid;
      isValid = validateOccupancy() && isValid;
      isValid = validateSelectField(districtInput, 'edit-listing-district-error', 'District is required') && isValid;
      isValid = validateSelectField(cityInput, 'edit-listing-city-error', 'City is required') && isValid;
      isValid = validateSelectField(categoryInput, 'edit-listing-category-error', 'Category is required') && isValid;
      isValid = validateSelectField(genderInput, 'edit-listing-gender-error', 'Preferred gender is required') && isValid;
      isValid = validateSelectField(statusInput, 'edit-listing-status-error', 'Status is required') && isValid;
      isValid = validateContactNumber() && isValid;
      isValid = validateImageCount() && isValid;
      isValid = validateFacilities() && isValid;

      if (!isValid) {
        event.preventDefault();
      }
    });
  }

  validateImageCount();
  validateFacilities();
});
