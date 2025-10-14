<div class="studyMaterial-post">
    <div class="studyMaterial-image">
        <img src="<?= htmlspecialchars($image ?? 'https://via.placeholder.com/400x200') ?>" alt="Post Image">
    </div>
    <div class="post-content>
        <h3 class="post-topic"><?= htmlspecialchars($topic) ?></h3>
        <p class="post-university"><?= htmlspecialchars($university) ?></p>
        <p class=post-views"><?= htmlspecialchars($views) ?> | <?= htmlspecialchars($views) ?></p>
        <p class="post-type"><?= htmlspecialchars($type) ?></p>
    </div>
</div>