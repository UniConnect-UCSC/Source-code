<?php
$friends = $friends ?? [];
?>

<div class="friends-container">
    <div class="friends-header">Friends</div>

    <?php if (empty($friends)): ?>
        <div class="friends-empty-card">
            <h3 class="friends-empty-title">No friends yet</h3>
            <p class="friends-empty-text">
                You do not have any friends at the moment. Start by searching for people and sending friend requests.
            </p>
            <a class="friends-empty-action" href="/search">Find Friends</a>
        </div>
    <?php else: ?>
        <?php foreach ($friends as $friend): ?>
            <?php $details = $friend->friend_details ?? null; ?>
            <?php if (!$details) continue; ?>

            <div class="friend-card">
                <a href="/users/<?php echo htmlspecialchars($details['id']); ?>">
                    <?php if (!empty($details['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($details['profile_picture']); ?>"
                            alt="<?php echo htmlspecialchars(($details['firstname'] ?? '') . ' ' . ($details['lastname'] ?? '')); ?>">
                    <?php else: ?>
                        <div class="friend-avatar">
                            <span><?php echo strtoupper(substr($details['firstname'] ?? '', 0, 1)); ?></span>
                            <span><?php echo strtoupper(substr($details['lastname'] ?? '', 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($details['firstname'] . ' ' . $details['lastname']); ?></h3>
                        <p><?php echo htmlspecialchars($details['university']); ?></p>
                    </div>
                </a>

                <div>
                    <button class="unfriend-button" onclick="unfriend(this, '<?php echo htmlspecialchars($details['id']); ?>')">
                        Unfriend
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>