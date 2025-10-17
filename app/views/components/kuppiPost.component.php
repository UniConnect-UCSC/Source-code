<div class="kuppi-post">
    <div class="post-image">
        <img src="<?= htmlspecialchars($image ?? 'https://via.placeholder.com/400x200') ?>" alt="Post Image">
    </div>
    <div class="post-content>
        <h3 class="post-topic"><?= htmlspecialchars($topic) ?></h3>
        <p class="post-university"><?= htmlspecialchars($university) ?></p>
        <p class=post-datetime"><?= htmlspecialchars($date) ?> | <?= htmlspecialchars($time) ?></p>
        <p class="post-platform">Platform: <?= htmlspecialchars($platform) ?></p>
    </div>
     <div class="post-actions">
            <a href="/kuppi/edit_kuppi/<?= urlencode($id) ?>" class="btn edit-btn">Edit</a>
            <a href="/kuppi/delete_kuppi/<?= urlencode($id) ?>" class="btn delete-btn"
               onclick="return confirm('Are you sure you want to delete this kuppi?');">
               Delete
            </a>
    </div>
    <?php if (!isset($id)) { echo "<!-- Kuppi ID is not set -->"; } ?>
</div>