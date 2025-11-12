<div class="modal" id="formEventModal">
<div class="modal-content">
    <div class="modal-header">
        <h2 id="modalHeaderName"> Header Placeholder </h2>
        <button class="close-btn" id="closeModalBtn">&times;</button>
    </div>
    <form id="eventForm">
        <div class="form-group">
            <label for="eventTitle">Event Title</label>
            <input type="text" id="eventTitle" name="title" required placeholder="Enter event title" >
        </div>
        <div class="form-group">
            <label for="eventDate">Date & Time</label>
            <input type="datetime-local" id="eventDate" name="event_timestamp" required>
        </div>
        <div class="form-group">
            <label for="eventHeldAt">Location</label>
            <input type="text" id="eventHeldAt" name="held_at" required placeholder="Enter event location">
        </div>
        <div class="form-group">
            <label for="eventDescription">Description</label>
            <textarea id="eventDescription" name="description" required placeholder="Describe your event"></textarea>
        </div>
        <div class="form-group">
            <label for="eventImage">Event Image (optional)</label>
            <input type="file" id="eventImage" name="event_image" accept="image/*">
            <div id="eventImagePreviewWrapper" class="image-preview-wrapper" style="display:none; margin-top: var(--spacing-2);">
                <img id="eventImagePreview" alt="Event image preview" class="image-preview" />
                <button type="button" id="clearEventImageBtn" class="btn btn-secondary small" style="margin-top: var(--spacing-2);">Remove image</button>
            </div>
        </div>
        <div class="form-group">
            <label for="categoryInput">Categories</label>
            <div class="category-picker" id="categoryPicker">
                <div class="tag-list" id="selectedCategories"></div>
                <input type="text" id="categoryInput" class="category-input" placeholder="Search categories..." />
                <div id="categorySuggestions" class="suggestions-list" ></div>
                <input type="hidden" id="eventCategories" name="eventCategories" value="[]" />
            </div>
            <small class="hint">Choose one or more. Type to search, click to add.</small>
        </div>
        <button type="submit" id="submitEventBtn" class="btn btn-primary" style="width: 100%;">Button Placeholder</button>
    </form>
</div>
</div>