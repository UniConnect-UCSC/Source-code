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
        <a class="view-profile" href="/users/<?php echo htmlspecialchars($userId); ?>"> View Profile </a>
        <?php $friend = false; ?>
        <?php if (!$friend): ?>
        <button class="send-friend-request" data-user-id="<?php echo htmlspecialchars($userId); ?>">
            Add Friend
        </button>
        <?php endif; ?>

    </div>
</div>