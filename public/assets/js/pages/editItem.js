document.addEventListener('DOMContentLoaded', function () {
  var form = document.querySelector('.edit-item-form');
  var imageInput = document.getElementById('image');
  var imageError = document.getElementById('edit-item-image-error');
  var titleInput = document.getElementById('title');
  var descriptionInput = document.getElementById('description');
  var priceInput = document.getElementById('price');
  var statusInput = document.getElementById('status');
  var categoryInput = document.getElementById('category_id');
  var contactInput = document.getElementById('contact_number');
  var contactError = document.getElementById('edit-item-contact-number-error');
  var selectedNewFiles = new DataTransfer();

  if (!form || !imageInput || !imageError) return;

  function showFieldError(input, errorId, message) {
    var errorDiv = document.getElementById(errorId);
    if (errorDiv) {
      errorDiv.textContent = message;
      errorDiv.classList.add('show');
    }

    if (input) {
      input.classList.add('error');
      input.classList.remove('success');
    }

    return false;
  }

  function showFieldSuccess(input, errorId) {
    var errorDiv = document.getElementById(errorId);
    if (errorDiv) {
      errorDiv.textContent = '';
      errorDiv.classList.remove('show');
    }

    if (input) {
      input.classList.remove('error');
      input.classList.add('success');
    }

    return true;
  }

  function validateRequiredTextField(input, errorId, message) {
    if (!input || !String(input.value || '').trim()) {
      return showFieldError(input, errorId, message);
    }

    return showFieldSuccess(input, errorId);
  }

  function validateRequiredSelectField(input, errorId, message) {
    if (!input || !String(input.value || '').trim()) {
      return showFieldError(input, errorId, message);
    }

    return showFieldSuccess(input, errorId);
  }

  function validatePrice() {
    var raw = priceInput ? String(priceInput.value || '').trim() : '';
    var amount = Number(raw);

    if (!raw) {
      return showFieldError(priceInput, 'edit-item-price-error', 'Price is required.');
    }

    if (Number.isNaN(amount) || amount <= 0) {
      return showFieldError(priceInput, 'edit-item-price-error', 'Price must be greater than 0.');
    }

    return showFieldSuccess(priceInput, 'edit-item-price-error');
  }

  function syncNewImageSelection() {
    imageInput.files = selectedNewFiles.files;
  }

  function validateImageCount() {
    var newCount = imageInput.files ? imageInput.files.length : 0;
    var remainingCurrentCount = document.querySelectorAll('.current-image-item').length;
    var totalCount = newCount + remainingCurrentCount;

    if (newCount > 5) {
      imageError.textContent = 'Maximum 5 new images are allowed.';
      imageError.classList.add('show');
      return false;
    }

    if (totalCount < 1) {
      imageError.textContent = 'At least one image is required.';
      imageError.classList.add('show');
      return false;
    }

    if (totalCount > 5) {
      imageError.textContent = 'Maximum 5 images are allowed in total.';
      imageError.classList.add('show');
      return false;
    }

    imageError.textContent = '';
    imageError.classList.remove('show');
    return true;
  }

  function normalizeContactNumber(value) {
    return String(value || '').replace(/\D+/g, '');
  }

  function validateContactNumber() {
    if (!contactInput || !contactError) return true;

    var normalized = normalizeContactNumber(contactInput.value);
    contactInput.value = normalized;

    if (!normalized) {
      contactError.textContent = 'Contact number is required.';
      contactError.classList.add('show');
      contactInput.classList.add('error');
      contactInput.classList.remove('success');
      return false;
    }

    if (normalized.length !== 10) {
      contactError.textContent = 'Please enter a valid contact number (exactly 10 digits).';
      contactError.classList.add('show');
      contactInput.classList.add('error');
      contactInput.classList.remove('success');
      return false;
    }

    contactError.textContent = '';
    contactError.classList.remove('show');
    contactInput.classList.remove('error');
    contactInput.classList.add('success');
    return true;
  }

  imageInput.addEventListener('change', function () {
    var incomingFiles = imageInput.files ? Array.from(imageInput.files) : [];
    var rejectedCount = 0;

    incomingFiles.forEach(function (file) {
      if (selectedNewFiles.files.length < 5) {
        selectedNewFiles.items.add(file);
      } else {
        rejectedCount++;
      }
    });

    syncNewImageSelection();

    if (rejectedCount > 0) {
      imageError.textContent = 'Maximum 5 new images are allowed.';
      imageError.classList.add('show');
      return;
    }

    validateImageCount();
  });

  var removeButtons = document.querySelectorAll('.remove-current-image-btn');
  var removeIdsContainer = document.getElementById('remove-image-ids-container');

  removeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var imageId = this.getAttribute('data-image-id');
      if (!imageId || !removeIdsContainer) return;

      var existingInput = removeIdsContainer.querySelector('input[value="' + imageId + '"]');
      if (!existingInput) {
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'remove_image_ids[]';
        hiddenInput.value = imageId;
        removeIdsContainer.appendChild(hiddenInput);
      }

      var imageItem = this.closest('.current-image-item');
      if (imageItem) {
        imageItem.remove();
      }

      validateImageCount();
    });
  });

  form.addEventListener('submit', function (e) {
    var isTitleValid = validateRequiredTextField(titleInput, 'edit-item-title-error', 'Item name is required.');
    var isDescriptionValid = validateRequiredTextField(descriptionInput, 'edit-item-description-error', 'Description is required.');
    var isPriceValid = validatePrice();
    var isStatusValid = validateRequiredSelectField(statusInput, 'edit-item-status-error', 'Status is required.');
    var isCategoryValid = validateRequiredSelectField(categoryInput, 'edit-item-category-error', 'Category is required.');
    var isContactValid = validateContactNumber();
    var isImageValid = validateImageCount();

    if (!isTitleValid || !isDescriptionValid || !isPriceValid || !isStatusValid || !isCategoryValid || !isContactValid || !isImageValid) {
      e.preventDefault();
    }
  });

  if (titleInput) {
    titleInput.addEventListener('input', function () {
      validateRequiredTextField(titleInput, 'edit-item-title-error', 'Item name is required.');
    });
  }

  if (descriptionInput) {
    descriptionInput.addEventListener('input', function () {
      validateRequiredTextField(descriptionInput, 'edit-item-description-error', 'Description is required.');
    });
  }

  if (priceInput) {
    priceInput.addEventListener('input', validatePrice);
  }

  if (statusInput) {
    statusInput.addEventListener('change', function () {
      validateRequiredSelectField(statusInput, 'edit-item-status-error', 'Status is required.');
    });
  }

  if (categoryInput) {
    categoryInput.addEventListener('change', function () {
      validateRequiredSelectField(categoryInput, 'edit-item-category-error', 'Category is required.');
    });
  }

  if (contactInput) {
    contactInput.addEventListener('input', function () {
      validateContactNumber();
    });
  }

  syncNewImageSelection();
});
