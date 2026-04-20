<div class="notification-icon" onclick="toggleNotifications()">
    <i data-lucide="bell" class="notification-icon"></i>
    <span class="notification-count hidden" id="notificationCount"></span>
</div>

<div class="notifications-wrapper">
    <div class="notifications-header">
        <button class="notification-filter-btn active" data-filter="all"
            onclick="filterNotifications('all', this.parentElement)">
            All
        </button>
        <button class="notification-filter-btn" data-filter="unread"
            onclick="filterNotifications('unread', this.parentElement)">
            Unread
        </button>
    </div>

    <!-- Handled by infinityScroll through ajax -->
    <div class="notifications-container" id="notificationsContainer"></div>

    <div class="notifications-footer">
        <button class="mark-all-read-btn" onclick="markAllAsRead()">
            <i data-lucide="check-check"></i>
            Mark all as read
        </button>
    </div>
</div>