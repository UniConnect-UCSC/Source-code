<div class="notifications-page">
    <header class="notifications-header">
        <h1>Notification Management</h1>
        <p class="subtitle">Send custom and test notifications</p>
    </header>

    <div class="notifications-content">
        <!-- Send Dummy Notification Section -->
        <section class="notification-card">
            <h2>🎲 Send Random Dummy Notification</h2>
            <p>Send a random notification from a predefined list of test notifications.</p>
            
            <div class="form-group">
                <label for="dummyUserId">User ID (optional):</label>
                <input type="number" id="dummyUserId" class="form-input" 
                       placeholder="Leave empty to send to yourself">
                <small class="form-help">If left empty, the notification will be sent to you (session user).</small>
            </div>

            <button id="sendDummyBtn" class="btn btn-primary btn-large">
                <span class="btn-icon">📨</span>
                Send Random Notification
            </button>

            <div id="dummyResponse" class="response-message"></div>
        </section>

        <!-- Send Custom Notification Section -->
        <section class="notification-card">
            <h2>✍️ Send Custom Notification</h2>
            <p>Create and send a custom notification with your own content.</p>
            
            <form id="customNotificationForm">
                <div class="form-group">
                    <label for="customUserId">User ID (optional):</label>
                    <input type="number" id="customUserId" class="form-input" 
                           placeholder="Leave empty to send to yourself">
                    <small class="form-help">If left empty, the notification will be sent to you (session user).</small>
                </div>

                <div class="form-group">
                    <label for="notificationType">Notification Type: <span class="required">*</span></label>
                    <select id="notificationType" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="event_info">Event Info</option>
                        <option value="system_info">System Info</option>
                        <option value="marketplace_info">Marketplace Info</option>
                        <option value="kuppi_info">Kuppi Info</option>
                        <option value="friend_request">Friend Request</option>
                        <option value="message">Message</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notificationTitle">Title: <span class="required">*</span></label>
                    <input type="text" id="notificationTitle" class="form-input" 
                           placeholder="Enter notification title" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="notificationMessage">Message: <span class="required">*</span></label>
                    <textarea id="notificationMessage" class="form-textarea" 
                              placeholder="Enter notification message" required maxlength="500" rows="4"></textarea>
                    <small class="form-help character-count">0 / 500 characters</small>
                </div>

                <div class="form-group">
                    <label for="notificationUrl">URL (optional):</label>
                    <input type="text" id="notificationUrl" class="form-input" 
                           placeholder="/events/example">
                    <small class="form-help">Optional URL to navigate when notification is clicked.</small>
                </div>

                <button type="submit" id="sendCustomBtn" class="btn btn-success btn-large">
                    <span class="btn-icon">🚀</span>
                    Send Custom Notification
                </button>
            </form>

            <div id="customResponse" class="response-message"></div>
        </section>

        <!-- Info Section -->
        <section class="notification-card info-card">
            <h2>ℹ️ Information</h2>
            <ul class="info-list">
                <li><strong>Dummy Notifications:</strong> Randomly selects from 25+ predefined test notifications.</li>
                <li><strong>Custom Notifications:</strong> Create personalized notifications with custom content.</li>
                <li><strong>User ID:</strong> Leave empty to send to yourself, or specify a user ID to send to another user.</li>
                <li><strong>Notification Types:</strong> Different types help categorize and style notifications appropriately.</li>
                <li><strong>URL:</strong> Adding a URL makes the notification clickable and navigable.</li>
            </ul>
        </section>
    </div>
</div>

<script src="/assets/js/pages/notifications.js"></script>
