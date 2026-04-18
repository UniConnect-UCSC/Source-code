<?php
?>
<div class="friend-actions-container">
    <?php if (($relationshipState ?? 'none') === 'friends'): ?>
        <div class="unfriend-actions">
            <button type="button"
                onclick="unfriend(this, '<?php echo htmlspecialchars($profileUser->id); ?>')">Unfriend</button>
        </div>

    <?php elseif (($relationshipState ?? 'none') === 'outgoing_pending'): ?>
        <div class="friend-request-actions">
            <button type="button"
                onclick="cancelFriendRequest(this, '<?php echo htmlspecialchars($profileUser->id); ?>')">Cancel
                Request</button>
        </div>

    <?php elseif (($relationshipState ?? 'none') === 'incoming_pending'): ?>
        <div class="accept-friend-request-actions">
            <button type="button" class="accept-button"
                onclick="acceptFriendRequest(this, '<?php echo htmlspecialchars($profileUser->id); ?>')">Accept</button>
            <button type="button" class="decline-button"
                onclick="declineFriendRequest(this, '<?php echo htmlspecialchars($profileUser->id); ?>')">Decline</button>
        </div>

    <?php else: ?>
        <div class="send-friend-request-actions">
            <button type="button" onclick="sendFriendRequest(this, '<?php echo htmlspecialchars($profileUser->id); ?>')">Add
                Friend</button>
        </div>
    <?php endif; ?>
</div>