<div class="kuppi-post" <?php if ($context === 'main'): ?> onclick="openKuppiModal(this)" style="cursor:pointer"
    <?php endif; ?>>

    <div class="post-image">
        <?php if (!empty($image)): ?>
        <img src="/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($topic ?? 'Kuppi') ?>" />
        <?php else: ?>
        <img src="/assets/images/ml-banner.jpg" alt="Default Kuppi Image" />
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

    <?php endif; ?>

    <?php if (isset($requester_name)): ?>
    <p class="post-requester-name"><?= htmlspecialchars($requester_name) ?></p>
    <?php endif; ?>
    <?php if (isset($requester_university)): ?>
    <p class="post-requester-university"><?= htmlspecialchars($requester_university) ?></p>
    <?php endif; ?>


    <?php if (($context === 'my_kuppis') || ($context === 'kuppi_requests')): ?>
    <div class="kuppi-menu">
        <button class="menu-btn" onclick="toggleKuppiMenu(this)">&#x22EE;</button>
        <div class="menu-dropdown" style="display:none;">
            <button type="button" onclick="openKuppiModal(this.closest('.kuppi-post-container'))">View</button>
            <a href="javascript:void(0);" onclick="openEditKuppiModal(<?= htmlspecialchars(json_encode([
                                                                                'id' => $id,
                                                                                'topic' => $topic,
                                                                                'category' => $category,
                                                                                'date' => $date ?? '',
                                                                                'time' => $time ?? '',
                                                                                'platform' => $platform ?? '',
                                                                                'link' => $link ?? '',
                                                                                'image_url' => $image ?? '',
                                                                            ]), ENT_QUOTES, 'UTF-8') ?>)">Edit</a>
            <a href="/kuppi/delete_kuppi/<?= urlencode($id) ?>"
                onclick="return confirm('Are you sure you want to delete this kuppi?');">Delete</a>
        </div>
    </div>
    <?php endif; ?>

</div>