<?php component("navbar"); ?>
  


    <div class="feed">
        <div class ="edit-listing-container">
            <div class="edit-listing-header">
       
            <h2>Edit Boarding Listing</h2>
            </div>
        
        <div class="edit-listing-form-container">
            
            <form action="/boardings/editListing" method="POST" enctype="multipart/form-data" class="edit-listing-form">
                <input type="hidden" name="room_id" value="<?= htmlspecialchars($room->id) ?>">
                <input type="hidden" name="submit_edit" value="1">
               
                <label for="rent">Monthly Rent (LKR):</label>
                <input type="number" id="rent" name="rent" min="0" step="100" value="<?= htmlspecialchars($room->rent) ?>" required><br>
                <div id="edit-listing-rent-error" class="error-message"></div>

                <label for="occupancy">Number of People:</label>
                <input type="number" id="occupancy" name="occupancy" min="1" max="10" value="<?= htmlspecialchars($room->occupancy) ?>" required><br>
                <div id="edit-listing-occupancy-error" class="error-message"></div>

                <?php
  $savedDistrict = $location->district ?? '';
  $savedCity = $location->city ?? '';

  $districts = [
    "Ampara","Anuradhapura","Badulla","Batticaloa","Colombo","Galle","Gampaha","Hambantota","Jaffna",
    "Kalutara","Kandy","Kegalle","Kilinochchi","Kurunegala","Mannar","Matale","Matara","Monaragala",
    "Mullaitivu","Nuwara Eliya","Polonnaruwa","Puttalam","Ratnapura","Trincomalee","Vavuniya",
  ];
?>

                <label for="district">District:</label>
<select id="district" name="district" required>
  <option value="" disabled <?= $savedDistrict === '' ? 'selected' : '' ?>>Select District</option>
  <?php foreach ($districts as $d): ?>
    <option value="<?= htmlspecialchars($d) ?>" <?= $savedDistrict === $d ? 'selected' : '' ?>>
      <?= htmlspecialchars($d) ?>
    </option>
  <?php endforeach; ?>
</select><br>
<div id="edit-listing-district-error" class="error-message"></div>

<label for="city">City:</label>
<select id="city" name="city" data-saved-city="<?= htmlspecialchars($savedCity) ?>" required>
  <option value="" disabled selected>Select City</option>
</select><br>
<div id="edit-listing-city-error" class="error-message"></div>

                <label for="description">Description (Optional):</label>
                <textarea id="description" name="description" ><?= htmlspecialchars($room->description ?? ' ' ) ?></textarea><br>

          
            <label for="category">Room Category:</label>
<select id="category" name="category" required>
    <option value="Single Room" <?= $room->category === 'Single Room' ? 'selected' : '' ?>>Single Room</option>
    <option value="Double Room" <?= $room->category === 'Double Room' ? 'selected' : '' ?>>Double Room</option>
    <option value="Shared Room" <?= $room->category === 'Shared Room' ? 'selected' : '' ?>>Shared Room</option>
    <option value="Apartment" <?= $room->category === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
    <option value="Hostel" <?= $room->category === 'Hostel' ? 'selected' : '' ?>>Hostel</option>
</select><br>
<div id="edit-listing-category-error" class="error-message"></div>

<label for="facilities">Facilities:</label>
<?php
    $selectedFacilities = array_map('trim', explode(',', strtolower((string)($room->facilities ?? ''))));
?>
<div id="room-facilities" class="facilities-multi-select">
    <button type="button" id="room-facilities-toggle" class="facilities-multi-select-toggle">Select Available Facilities</button>
    <div id="room-facilities-menu" class="facilities-multi-select-menu">
        <label><input type="checkbox" name="facilities[]" value="wifi" <?= in_array('wifi', $selectedFacilities, true) ? 'checked' : '' ?>> WiFi</label>
        <label><input type="checkbox" name="facilities[]" value="meals available" <?= in_array('meals available', $selectedFacilities, true) ? 'checked' : '' ?>> Meals Available</label>
        <label><input type="checkbox" name="facilities[]" value="kitchen access" <?= in_array('kitchen access', $selectedFacilities, true) ? 'checked' : '' ?>> Kitchen Access</label>
        <label><input type="checkbox" name="facilities[]" value="common bathroom" <?= in_array('common bathroom', $selectedFacilities, true) ? 'checked' : '' ?>> Common Bathroom</label>
        <label><input type="checkbox" name="facilities[]" value="attached bathroom" <?= in_array('attached bathroom', $selectedFacilities, true) ? 'checked' : '' ?>> Attached Bathroom</label>
        <label><input type="checkbox" name="facilities[]" value="ac" <?= in_array('ac', $selectedFacilities, true) ? 'checked' : '' ?>> AC</label>
        <label><input type="checkbox" name="facilities[]" value="fully furnished" <?= in_array('fully furnished', $selectedFacilities, true) ? 'checked' : '' ?>> Fully Furnished</label>
    </div>
</div><br>

<label for="gender">Preferred Gender:</label>
<select id="gender" name="gender" required>
    <option value="male students" <?= strtolower($room->gender) === 'male students' ? 'selected' : '' ?>>Male students</option>
    <option value="female students" <?= strtolower($room->gender) === 'female students' ? 'selected' : '' ?>>Female students</option>
    <option value="male workers" <?= strtolower($room->gender) === 'male workers' ? 'selected' : '' ?>>Male workers</option>
    <option value="female workers" <?= strtolower($room->gender) === 'female workers' ? 'selected' : '' ?>>Female workers</option>
    <option value="any" <?= strtolower($room->gender) === 'any' ? 'selected' : '' ?>>Any</option>
</select><br>
<div id="edit-listing-gender-error" class="error-message"></div>

              

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <?php foreach ($statusOptions as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= $room->status === $status ? 'selected' : '' ?>>
                             <?= ucfirst($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                <div id="edit-listing-status-error" class="error-message"></div>

                <label for="contact_number">Contact Number:</label>
<input type="text" id="contact_number" name="contact_number"
       value="<?= htmlspecialchars($room->contact_number ?? '') ?>" required><br>
<div id="edit-listing-contact-number-error" class="error-message"></div>

                <label for="image">Upload New Images (Optional):</label>
                <input type="file" id="image" name="images[]" accept="image/*" multiple><br>
                <div id="edit-listing-image-error" class="error-message"></div>

                <?php if (!empty($currentImages)): ?>
                <div class="current-image">
                    <p>Current Images:</p>
                    <div class="current-image-grid">
                        <?php foreach ($currentImages as $image): ?>
                            <div class="current-image-item" data-image-id="<?= htmlspecialchars((string)($image['id'] ?? '')) ?>">
                                <img class="current-image-thumb" src="<?= htmlspecialchars($image['url']) ?>" alt="Current listing image">
                                <?php if (!empty($image['id'])): ?>
                                    <button type="button" class="remove-current-image-btn" data-image-id="<?= htmlspecialchars((string)$image['id']) ?>" aria-label="Remove image">&times;</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="remove-image-ids-container"></div>
                </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn submit-btn">Update Listing</button>
                    <a href="/boardings/myListings" class="btn cancel-btn">Cancel</a>
                </div>
            </form>
            <script src="/assets/js/pages/editListing.js"></script>
        </div>
    </div>
   
</div>


