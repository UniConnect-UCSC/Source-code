<div class="user-card">
    <a href="/users/<?php echo htmlspecialchars($userId); ?>" class="user-card-link">
        <div class="user-card-avatar">
            <?php if (!empty($profilePicUrl)): ?>
                <img src="<?php echo htmlspecialchars($profilePicUrl); ?>">
            <?php else: ?>
                <div class="default-avatar">
                    <span><?php echo strtoupper(substr($name, 0, 1)); ?></span>
                    <span><?php echo strtoupper(substr($name, 1, 1)); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="user-card-info">
            <h3><?php echo htmlspecialchars($name); ?></h3>
            <p><?php echo htmlspecialchars($universityName); ?></p>
        </div>
    </a>

    <div class="button-container">

        <a class="view-profile" href="/users/<?php echo htmlspecialchars($userId); ?>">View Profile</a>

        <?php if (($friendshipStatus ?? null) === 'accepted'): ?>
            <button class="friend-button" disabled>Friends</button>

        <?php elseif (($friendshipStatus ?? null) === 'pending'): ?>
            <button class="pending-friend-request">Pending</button>

        <?php else: ?>
            <form method="POST" action="/search/sendFriendRequest" style="display: inline;">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                <button type="submit" class="send-friend-request">Add Friend</button>
            </form>
        <?php endif; ?>
    </div>
</div>