<?php component("navbar"); ?>
<div class="home-layout">
    <?php component("navPanel"); ?>
    <div class="feed">
        <div class="feed-header">
            <h2>Post a Boarding Listing</h2>
        </div>
        <div class="post-listing-form-container">
            <form action="/boardings/createListing" method="POST" enctype="multipart/form-data" id="post-listing-form" class="post-listing-form">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br>
                    
                <label for="room-rent">Monthly Rent (LKR):</label>
                <input type="number" id="room-rent" name="rent" min="0" step="100" required><br>
                <div id="post-listing-rent-error" class="error-message"></div>
                
                <label for="room-occupancy">Number of People:</label>
                <input type="number" id="room-occupancy" name="occupancy" min="1" max="10" required><br>
                <div id="post-listing-occupancy-error" class="error-message"></div>

                <label for="room-city">City:</label>
                <input type="text" id="room-city" name="city" required><br>
                <div id="post-listing-city-error" class="error-message"></div>

                <label for="room-district">District:</label>
                <input type="text" id="room-district" name="district" required><br>
                <div id="post-listing-district-error" class="error-message"></div>

                <label for="room-description">Description (Optional):</label>
                <textarea id="room-description" name="description"></textarea><br>
                <div id="post-listing-description-error" class="error-message"></div>


                <label for="room-category">Category:</label>
                <select id="room-category" name="category" required>
                    <option value="Single Room">Single Room</option>
                    <option value="Double Room">Double Room</option>
                    <option value="Shared Room">Shared Room</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Hostel">Hostel</option>
                </select><br>
                <div id="post-listing-category-error" class="error-message"></div>

                <label for="room-facilities">Facilities:</label>
                
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
                </div><br>
                <div id="post-listing-facilities-error" class="error-message"></div>

                <label for="room-status">Status:</label>
                <select id="room-status" name="status" required>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="closed">Closed</option>
                </select><br>
                <div id="post-listing-status-error" class="error-message"></div>

                <label for="room-gender">Preferred Gender:</label>
                <select id="room-gender" name="gender" required>
                    <option value="male students">Male students</option>
                    <option value="female students">Female students</option>
                    <option value="male workers">Male workers</option>
                    <option value="female workers">Female workers</option>
                    <option value="any">Any</option>
                </select><br>
                <div id="post-listing-gender-error" class="error-message"></div>

                <label for="room-contact-number">Contact Number:</label>
<input type="text" id="room-contact-number" name="contact_number" required><br>
<div id="post-listing-contact-number-error" class="error-message"></div>

                <label for="post-listing-media-input">Upload Image:</label>
                <input type="file" id="post-listing-media-input" name="images" accept="image/*"><br>
                <div id="post-listing-media-error" class="error-message"></div>

                <button type="submit" class="btn submit-btn">Post Listing</button>
            </form>
        </div>
    </div>
<?php component("widgetPanel"); ?>
</div>