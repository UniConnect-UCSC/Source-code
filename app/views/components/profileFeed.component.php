<?php require_once(__DIR__ . "/../../models/GlobalPost.php"); ?>
<?php require_once(__DIR__ . "/../../models/UniversityPost.php"); ?>

<?php
$userId = $profileUser->id ?? $_SESSION['user_id'] ?? null;
$globalModel = new GlobalPost();
$globalPosts = $globalModel->where(
    [
        ["user_id", '=', $userId]
    ],
    null,
    null,
    ['created_at' => 'DESC']
);

$uniModel = new UniversityPost();
$uniPosts = $uniModel->where(
    [
        ["user_id", '=', $userId]
    ],
    null,
    null,
    ['created_at' => 'DESC']
);
?>
<div class="profile-feed" data-user-id="<?= htmlspecialchars($userId) ?>">

    <div class="profile-feed-list profile-feed-global" aria-label="Global posts">
        <?php if (empty($globalPosts)): ?>
        <p class="no-results-message">No global posts found.</p>
        <?php else: ?>
        <?php foreach ($globalPosts as $post): ?>
        <?php
                component("post", [
                    "postId" => $post->id,
                    "authorId" => $post->user_id,
                    "author" => $post->is_anonymous ? "Anonymous" : ($_SESSION['user_fName'] ?? '') . " " . ($_SESSION['user_lName'] ?? ''),
                    "caption" => $post->caption,
                    "createdAt" => $post->created_at,
                    "updatedAt" => $post->updated_at,
                    "mediaUrl" => $post->media_url,
                    "isAnonymous" => $post->is_anonymous,
                    "postType" => "global"
                ]);
                ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="profile-feed-list profile-feed-university" aria-label="University posts" style="display:none;">
        <?php if (empty($uniPosts)): ?>
        <p class="no-results-message">No university posts found.</p>
        <?php else: ?>
        <?php foreach ($uniPosts as $post): ?>
        <?php
                component("post", [
                    "postId" => $post->id,
                    "authorId" => $post->user_id,
                    "author" => $post->is_anonymous ? "Anonymous" : ($_SESSION['user_fName'] ?? '') . " " . ($_SESSION['user_lName'] ?? ''),
                    "caption" => $post->caption,
                    "createdAt" => $post->created_at,
                    "updatedAt" => $post->updated_at,
                    "mediaUrl" => $post->media_url,
                    "isAnonymous" => $post->is_anonymous,
                    "postType" => "university",
                ]);
                ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>