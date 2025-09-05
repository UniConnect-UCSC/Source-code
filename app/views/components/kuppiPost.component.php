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
</div>