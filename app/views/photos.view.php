<?php
require_once(__DIR__ . "/../models/GlobalPost.php");
?>

<?php
$globalPostModel = new GlobalPost();

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $sql = "
        SELECT media_url, created_at, 'global' AS source
        FROM global_posts
        WHERE user_id = :user_id

        UNION ALL

        SELECT media_url, created_at, 'university' AS source
        FROM university_posts
        WHERE user_id = :user_id

        ORDER BY created_at DESC
    ";

    $posts = $globalPostModel->query($sql, ['user_id' => $user_id]);
} else {
    $posts = [];
}
?>

<?php component("navbar"); ?>

<div class="photos-layout">
    <?php component("navPanel"); ?>

    <div class="photos-container">
        <h2>Your Photos</h2>

        <div class="photos-grid">
            <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
            <div class="photo-item">
                <img src="<?php echo htmlspecialchars($post->media_url); ?>" alt="Photo">
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No photos found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>