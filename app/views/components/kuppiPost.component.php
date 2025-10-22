
<div class="kuppi-post">

    <div class="post-image">
        <?php if (!empty($image)): ?>
        <img src="/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($topic ?? 'Kuppi') ?>" />
        <?php endif; ?>
    </div>
    <div class="post-content">
        <?php if (!empty($topic)): ?>
            <h3 class="post-topic"><?= htmlspecialchars($topic) ?></h3>
        <?php endif; ?>
        <?php if (isset($university)): ?>
            <p class="post-university"><?= htmlspecialchars($university ?? 'Unknown University') ?></p>
        <?php else: ?>
            <p class="post-university">Unknown University</p>
        <?php endif; ?>
        <?php if (isset($category)): ?>
            <p class="post-category"><?= htmlspecialchars($category) ?></p>
        <?php endif; ?>
        <?php if (isset($date) || isset($time)): ?>
            <p class="post-datetime">
                <?= htmlspecialchars($date ?? '') ?>
                <?php if (!empty($date) && !empty($time)): ?>
                    | 
                <?php endif; ?>
                <?= htmlspecialchars($time ?? '') ?>
            </p>
        <?php endif; ?>
        <?php if (isset($platform)): ?>
            <p class="post-platform">Platform: <?= htmlspecialchars($platform) ?></p>
        <?php endif; ?>
    </div>
    <?php if (isset($status)): ?>
    <div class="post-status"><?= htmlspecialchars($status) ?></div>
    <?php endif; ?>

    <?php
    // Only show Edit/Delete if user is host or requester
    if (
        isset($_SESSION['user_id']) && ((isset($host_id) || isset($requester_id))) &&
        ($_SESSION['user_id'] == $host_id || $_SESSION['user_id'] == $requester_id)
    ): ?>
    <div class="post-actions">
        <a href="/kuppi/edit_kuppi/<?= urlencode($id) ?>" class="btn edit-btn">Edit</a>
        <a href="/kuppi/delete_kuppi/<?= urlencode($id) ?>" class="btn delete-btn"
           onclick="return confirm('Are you sure you want to delete this kuppi?');">
           Delete
        </a>
    </div>
    <?php endif; ?>

    <?php if (isset($requester_name)): ?>
    <p class="post-requester-name"><?= htmlspecialchars($requester_name) ?></p>
    <?php endif; ?>
    <?php if (isset($requester_university)): ?>
    <p class="post-requester-university"><?= htmlspecialchars($requester_university) ?></p>
    <?php endif; ?>
</div>