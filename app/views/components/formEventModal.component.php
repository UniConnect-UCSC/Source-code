<div class="modal" id="formEventModal">
<div class="modal-content">
    <div class="modal-header">
        <h2 id="modalHeaderName"> Header Placeholder </h2>
        <button class="close-btn" id="closeModalBtn">&times;</button>
    </div>
    <form id="eventForm">
        <div class="form-group">
            <label for="eventTitle">Event Title</label>
            <input type="text" id="eventTitle" required placeholder="Enter event title" >
        </div>
        <div class="form-group">
            <label for="eventDate">Date & Time</label>
            <input type="datetime-local" id="eventDate" required>
        </div>
        <div class="form-group">
            <label for="eventHeldAt">Location</label>
            <input type="text" id="eventHeldAt" required placeholder="Enter event location">
        </div>
        <div class="form-group">
            <label for="eventDescription">Description</label>
            <textarea id="eventDescription" required placeholder="Describe your event"></textarea>
        </div>
        <button type="submit" id="submitEventBtn" class="btn btn-primary" style="width: 100%;">Button Placeholder</button>
    </form>
</div>
</div>