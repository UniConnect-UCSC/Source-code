<div class="notification-icon" onclick="toggleNotifications()">
    <i data-lucide="bell" class="notification-icon"></i>
</div>

<div class="notifications-wrapper">
<!-- Handled by infinityScroll through ajax -->
    <div class="notifications-container" id="notificationsContainer"></div>

    <div class="notifications-footer">
        <button class="mark-all-read-btn" onclick="markAllAsRead()">
            <i data-lucide="check-check"></i>
            Mark all as read
        </button>
    </div>
</div>