<?php
$photos = $photos ?? [];
$profileUser = $profileUser ?? null;
$heading = $profileUser ? "Photos from " . htmlspecialchars($profileUser->f_name) : "Your Photos";
?>

<?php component("navbar"); ?>

<div class="photos-layout">
    <?php component("navPanel"); ?>

    <div class="photos-container">

        <h2><?php echo htmlspecialchars($heading); ?></h2>

        <div class="photos-grid">
            <?php if (!empty($photos)): ?>
                <?php foreach ($photos as $post): ?>
                    <?php $mediaUrl = is_array($post) ? ($post['media_url'] ?? '') : ($post->media_url ?? ''); ?>
                    <?php if ($mediaUrl === '') continue; ?>
                    <div class="photo-item">
                        <img src="<?php echo htmlspecialchars($mediaUrl); ?>" alt="Photo">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No photos found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>