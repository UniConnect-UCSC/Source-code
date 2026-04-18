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

        <?php if (($relationshipState ?? 'none') === 'friends'): ?>
            <button class="friend-button" disabled>
                Friends
            </button>

        <?php elseif (($relationshipState ?? 'none') === 'outgoing_pending'): ?>
            <button class="pending-friend-request"
                onclick="cancelFriendRequest(this, '<?php echo htmlspecialchars($userId); ?>')">
                Cancel Request
            </button>

        <?php elseif (($relationshipState ?? 'none') === 'incoming_pending'): ?>
            <div class="accept-friend-request-actions">
                <button type="button" class="accept-button"
                    onclick="acceptFriendRequest(this, '<?php echo htmlspecialchars($userId); ?>')">
                    Accept
                </button>
                <button type="button" class="decline-button"
                    onclick="declineFriendRequest(this, '<?php echo htmlspecialchars($userId); ?>')">
                    Decline
                </button>
            </div>

        <?php else: ?>
            <button class="send-friend-request"
                onclick="sendFriendRequest(this, '<?php echo htmlspecialchars($userId); ?>')">
                Add Friend
            </button>
        <?php endif; ?>
    </div>
</div>