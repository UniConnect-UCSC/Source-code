<!-- Some dummy notifications about post interactions and upcoming events and friend requests -->
<?php
$notifications = [
    [
        "message" => "Your post received a new like",
        "time" => "2 hours ago",
        "from" => "John Doe",
        "read" => false,
    ],
    [
        "message" => "Friend request.",
        "time" => "3 days ago",
        "from" => "Anna Johnson",
        "read" => false,
    ],
    [
        "message" => "Don't forget the event tomorrow!",
        "time" => "5 days ago",
        "from" => "Event Organizer",
        "read" => true,
    ],
    [
        "message" => "Your post received a new comment",
        "time" => "1 day ago",
        "from" => "Jane Smith",
        "read" => true,
    ],

]
?>

<div class="notification-icon" onclick="toggleNotifications()">
    <i data-lucide="bell" class="notification-icon"></i>
</div>

<div class="notifications-container">
    <ul>
        <?php foreach ($notifications as $notification): ?>
        <li <?php if (!$notification['read']) echo 'class="unread-notification"'; ?>>

            <?php if (!$notification['read']): ?>
            <?php echo '<i data-lucide="dot" class="unread-indicator"></i>'; ?>
            <?php endif; ?>

            <div class="<?php if ($notification['read']) echo 'add-padding'; ?>">
                <?php echo htmlspecialchars($notification['message']); ?> - from
                <?php echo htmlspecialchars($notification['from']) ?>
                <br>
                <?php echo htmlspecialchars($notification['time']); ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>