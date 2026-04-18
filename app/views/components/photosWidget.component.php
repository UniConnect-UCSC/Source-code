<?php
require_once(__DIR__ . "/../../models/GlobalPost.php");
require_once(__DIR__ . "/../../models/UniversityPost.php");

$profileUser = $profileUser ?? null;
$userId = $profileUser->id ?? $_SESSION['user_id'] ?? null;
$isOwnProfile = isset($_SESSION['user_id']) && $userId === $_SESSION['user_id'];

$photos = [];

if ($userId) {
    // Fetch global posts with media
    $globalModel = new GlobalPost();
    $globalPosts = $globalModel->where(
        [
            ["user_id", '=', $userId]
        ],
        6,  // limit to 6 for testing
        null,
        ['created_at' => 'DESC']
    );

    echo "<!-- Global posts found: " . ($globalPosts ? count($globalPosts) : '0') . " -->";

    // Fetch university posts with media
    $uniModel = new UniversityPost();
    $uniPosts = $uniModel->where(
        [
            ["user_id", '=', $userId]
        ],
        6,  // limit to 6 for testing
        null,
        ['created_at' => 'DESC']
    );

    echo "<!-- Uni posts found: " . ($uniPosts ? count($uniPosts) : '0') . " -->";

    // Combine both arrays
    $allPosts = array_merge($globalPosts ?: [], $uniPosts ?: []);

    // Build photos array from media_urls
    foreach ($allPosts as $post) {
        if (!$isOwnProfile && !empty($post->is_anonymous)) {
            continue;
        }

        if (!empty($post->media_url)) {
            $photos[] = (object)[
                'media_url' => $post->media_url,
                'post_id' => $post->id
            ];
        }
    }
}

?>
<div class="photos-widget">
    <div>
        <h2>Photos</h2>
    </div>

    <?php if (empty($photos)): ?>
        <p class="no-photos-message">No photos</p>
    <?php else: ?>
        <div class="photos-grid">
            <?php foreach ($photos as $photo): ?>
                <a class="photo" href="/">
                    <img src="<?= htmlspecialchars($photo->media_url) ?>" alt="Photo" />
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a class="see-all-photos" href="/photos/<?= htmlspecialchars($profileUser->id ?? '') ?>">
        See All
    </a>
</div>