<?php
require_once(__DIR__ . "/../models/BoardingRoom.php");
require_once(__DIR__ . "/../models/BoardingLocations.php");

$roomsModel = new BoardingRoom();
$rooms = $roomsModel->where([['status', '!=', 'closed']], null, null, ['created_at' => 'DESC']);

$locationModel = new Location();
$locations = $locationModel->where([]);
?>

<?php component("navbar"); ?>

<div class="boardings-layout">
    <?php component("navPanel"); ?>
    <div class="boardings-container">
        <div class="boardings-header">
            <h2>Rooms & Boardings</h2>
            <div class="boardings-header-buttons">
                
                
                <div class="btn-postListing" onclick="openPostListingModal()">Post a Listing</div>
                <a href="/boardings/myListings" class="btn-myListings">My Listings</a>    
            </div>
        </div>





     <!-- Search and Filter Section -->
        <div class="boardings-search-section">
            <div class="search-bar-container">
                <i data-lucide="search"></i>
                <input type="text" id="search-input" placeholder="Search by city or district..." />
            </div>

            <div class="filter-toggle" onclick="toggleFilters()">
                <i data-lucide="sliders-horizontal"></i>
                <span>Filters</span>
            </div>

            <div class="filters-container" id="filters-container">
                <div class="filter-group">
                    <label>City</label>
                    <input type="text" id="filter-city" placeholder="Enter city" />
                </div>

                <div class="filter-group">
                    <label>District</label>
                    <input type="text" id="filter-district" placeholder="Enter district" />
                </div>

                <div class="filter-group">
                    <label>Gender</label>
                    <select id="filter-gender">
                        <option value="">All Genders</option>
                        <option value="male students">Male students</option>
                        <option value="female students">Female students</option>
                        <option value="male workers">Male workers</option>
                        <option value="female workers">Female workers</option>
                        <option value="any">Any</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Category</label>
                    <select id="filter-category">
                        <option value="">All Categories</option>
                        <option value="single room">Single Room</option>
                        <option value="double room">Double Room</option>
                        <option value="shared room">Shared Room</option>
                        <option value="apartment">Apartment</option>
                        <option value="hostel">Hostel</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Facilities</label>
                    <select id="filter-facilities">
                        <option value="">All Facilities</option>
                        <option value="wifi">WiFi</option>
                        <option value="meals available">Meals Available</option>
                        <option value="kitchen access">Kitchen Access</option>
                        <option value="common bathroom">Common Bathroom</option>
                        <option value="attached bathroom">Attached Bathroom</option>
                        <option value="ac">AC</option>
                        <option value="fully furnished">Fully Furnished</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Min Rent (LKR)</label>
                    <input type="number" id="filter-min-rent" placeholder="0" min="0" step="1000" />
                </div>

                <div class="filter-group">
                    <label>Max Rent (LKR)</label>
                    <input type="number" id="filter-max-rent" placeholder="100000" min="0" step="1000" />
                </div>

                <div class="filter-actions">
                    <button class="btn-apply-filters" onclick="applyFilters()">Apply Filters</button>
                    <button class="btn-clear-filters" onclick="clearFilters()">Clear</button>
                </div>
            </div>
        </div>


        

        
        <!-- Add id attribute -->
<div class="boarding-items" id="boarding-items">
            <?php
            if (!empty($rooms)) {
                foreach ($rooms as $room) {
                    component("boardingCard", [
                        "id" => $room->id,
                        "studentID" => $room->student_id,
                        "rent" => $room->rent,
                        "occupancy" => $room->occupancy,
                        "gender" => $room->gender,
                        "location_id" => $room->location_id,
                        "description" => $room->description ?? null,
                        "status" => $room->status,
                        "category" => $room->category ?? null,
                        "facilities" => $room->facilities ?? null,
                        "contact_number" => $room->contact_number ?? null,
                       
                        "image_url" => $room->image_url ?? null,
                        "created_at" => $room->created_at,
                    ]);
                }
            } else {
                echo '<p class="no-listings">No boarding listings available at the moment.</p>';
            }
            ?>
        </div>
             
        </div>
    </div>
</div>






<div class="post-listing-modal" id="post-listing-modal" onclick="closePostListingModal()">
    <div class="post-listing-modal-content" onclick="event.stopPropagation()">
        <div class="post-listing-modal-header">
            <h2>Post a Boarding Listing</h2>
        </div>
       <form id="post-listing-form" enctype="multipart/form-data" autocomplete="off" method="POST">
    <div class="post-listing-modal-post-content">

        <!-- Rent Input -->
        <div>
            <input type="number" name="rent" id="room-rent" placeholder="Monthly Rent (LKR)" min="0" step="100" required>
            <div class="error-message" id="post-listing-rent-error"></div>
        </div>

        <!-- Occupancy Input -->
        <div>
            <input type="number" name="occupancy" id="room-occupancy" placeholder="Number of People" min="1" max="10" required>
            <div class="error-message" id="post-listing-occupancy-error"></div>
        </div>


        <div>
            <input type="text" name="description" id="room-description" placeholder="Description (Optional)">
            <div class="error-message" id="post-listing-description-error"></div>
        </div>

         

      
<!-- District -->
<div>
  <select name="district" id="room-district" required>
    <option value="" disabled selected>Select District</option>
    <option value="Ampara">Ampara</option>
    <option value="Anuradhapura">Anuradhapura</option>
    <option value="Badulla">Badulla</option>
    <option value="Batticaloa">Batticaloa</option>
    <option value="Colombo">Colombo</option>
    <option value="Galle">Galle</option>
    <option value="Gampaha">Gampaha</option>
    <option value="Hambantota">Hambantota</option>
    <option value="Jaffna">Jaffna</option>
    <option value="Kalutara">Kalutara</option>
    <option value="Kandy">Kandy</option>
    <option value="Kegalle">Kegalle</option>
    <option value="Kilinochchi">Kilinochchi</option>
    <option value="Kurunegala">Kurunegala</option>
    <option value="Mannar">Mannar</option>
    <option value="Matale">Matale</option>
    <option value="Matara">Matara</option>
    <option value="Monaragala">Monaragala</option>
    <option value="Mullaitivu">Mullaitivu</option>
    <option value="Nuwara Eliya">Nuwara Eliya</option>
    <option value="Polonnaruwa">Polonnaruwa</option>
    <option value="Puttalam">Puttalam</option>
    <option value="Ratnapura">Ratnapura</option>
    <option value="Trincomalee">Trincomalee</option>
    <option value="Vavuniya">Vavuniya</option>
  </select>
  <div class="error-message" id="post-listing-district-error"></div>
</div>

<!-- City -->
<div>
  <select name="city" id="room-city" required disabled>
    <option value="" disabled selected>Select City</option>
  </select>
  <div class="error-message" id="post-listing-city-error"></div>
</div>

        <!-- Room Category -->
<div>
    <select name="category" id="room-category" required>
        <option value="" disabled selected>Select Room Category</option> 
        <option value="single Room">Single Room</option>
        <option value="double Room">Double Room</option>
        <option value="shared Room">Shared Room</option>
        <option value="apartment">Apartment</option>
        <option value="hostel">Hostel</option>
    </select>
    <div class="error-message" id="post-listing-category-error"></div>
</div>

<!-- Facilities -->
<div>
    <div id="room-facilities" class="facilities-multi-select">
        <button type="button" id="room-facilities-toggle" class="facilities-multi-select-toggle">Select Available Facilities</button>
        <div id="room-facilities-menu" class="facilities-multi-select-menu">
            <label><input type="checkbox" name="facilities[]" value="wifi"> WiFi</label>
            <label><input type="checkbox" name="facilities[]" value="meals available"> Meals Available</label>
            <label><input type="checkbox" name="facilities[]" value="kitchen access"> Kitchen Access</label>
            <label><input type="checkbox" name="facilities[]" value="common bathroom"> Common Bathroom</label>
            <label><input type="checkbox" name="facilities[]" value="attached bathroom"> Attached Bathroom</label>
            <label><input type="checkbox" name="facilities[]" value="ac"> AC</label>
            <label><input type="checkbox" name="facilities[]" value="fully furnished"> Fully Furnished</label>
        </div>
    </div>
    <div class="error-message" id="post-listing-facilities-error"></div>
</div>

    <div>
        <select name="gender" id="room-gender" required>
            <option value="" disabled selected>Select Preferred Gender</option>
            <option value="male students">Male students</option>
            <option value="female students">Female students</option>
            <option value="male workers">Male workers</option>
            <option value="female workers">Female workers</option>
            <option value="any">Any</option>
        </select>
        <div class="error-message" id="post-listing-gender-error"></div>
    </div>




        <!-- Status Select -->
        <div>
            <select name="status" id="room-status" required>
                <option value="" disabled selected>Select Status</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
               
            </select>
            <div class="error-message" id="post-listing-status-error"></div>
        </div>

        <div>
  <input type="text" name="contact_number" id="room-contact-number" placeholder="Contact Number" required>
  <div class="error-message" id="post-listing-contact-number-error"></div>
</div>

        <!-- Image Upload -->
        <div class="post-listing-media-input">
            <input id="post-listing-media-input" name="images[]" type="file" accept="image/*" multiple />
        </div>

        <!-- Image Preview -->
        <div class="post-listing-image-preview" id="post-listing-image-preview"></div>
        
        <!-- Add Photo Button -->
        <!--<div  class="post-listing-image-button">
            <input type="file" name="photo" id="upload-photo-input" style="display:none;" accept="image/*">
            <i data-lucide="image"></i>
            <label for="upload-photo-input">Add Photo</label>
        </div>-->
         <div onclick="openPostListingImageSelector()" class="post-listing-image-button">
            <i data-lucide="image"></i>
            Add Photos
        </div> 
        <div class="error-message" id="post-listing-media-error"></div>

    </div>
    
    <!-- Submit Button -->
    <div class="post-listing-modal-actions">
        <button type="submit" class="post-listing-modal-button">Post Listing</button>
    </div>
</form>

        <div class="loading-spinner" id="post-listing-loading-spinner" style="display:none;">
            <div class="spinner"></div>
        </div>
    </div>
</div>