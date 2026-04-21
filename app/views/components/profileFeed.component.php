<?php require_once(__DIR__ . "/../../models/GlobalPost.php"); ?>
<?php require_once(__DIR__ . "/../../models/UniversityPost.php"); ?>
<?php require_once(__DIR__ . "/../../models/Reaction.php"); ?>

<?php
$profileUser = $profileUser ?? null;
$loggedInUserId = $_SESSION['user_id'] ?? null;
$profileUserId = $profileUser->id ?? null;
$userId = $profileUserId ?? $loggedInUserId;
$userFName = $profileUser->f_name ?? $_SESSION['user_fName'] ?? '';
$userLName = $profileUser->l_name ?? $_SESSION['user_lName'] ?? '';
$isOwnProfile = $loggedInUserId && ($profileUserId ? $loggedInUserId === $profileUserId : true);

$reactionModel = new Reaction();
$globalModel = new GlobalPost();
$uniModel = new UniversityPost();

$globalPosts = $globalModel->where(
    [
        ["user_id", '=', $userId]
    ],
    null,
    null,
    ['created_at' => 'DESC'],
    [],
    [],
    false
);


$uniPosts = $uniModel->where(
    [
        ["user_id", '=', $userId]
    ],
    null,
    null,
    ['created_at' => 'DESC'],
    [],
    [],
    false
);
?>
<div class="profile-feed" data-user-id="<?= htmlspecialchars($userId) ?>">

    <div class="profile-feed-list profile-feed-global" aria-label="Global posts">
        <?php if (empty($globalPosts)): ?>
            <p class="no-results-message">No global posts found.</p>

        <?php else: ?>
            <?php foreach ($globalPosts as $post): ?>
                <?php if (!$isOwnProfile && !empty($post->is_anonymous)) continue; ?>
                <?php
                component("post", [
                    "postId" => $post->id,
                    "profilePic" => $_SESSION['user_profilePicture'] ?? null,
                    "authorId" => $post->user_id,
                    "author" => $post->is_anonymous ? "Anonymous" : $userFName . " " . $userLName,
                    "userFName" => $userFName,
                    "userLName" => $userLName,
                    "caption" => $post->caption,
                    "createdAt" => $post->created_at,
                    "updatedAt" => $post->updated_at,
                    "mediaUrl" => $post->media_url,
                    "isAnonymous" => $post->is_anonymous,
                    "postType" => "global",
                    "profileUser" => $profileUser,
                    "reactionCount" => $reactionModel->countReactions($post->id),
                    "userHasReacted" => $reactionModel->getReaction($loggedInUserId, $post->id) ? true : false,
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
                <?php if (!$isOwnProfile && !empty($post->is_anonymous)) continue; ?>
                <?php
                component("post", [
                    "postId" => $post->id,
                    "profilePic" => $_SESSION['user_profilePicture'] ?? null,
                    "authorId" => $post->user_id,
                    "author" => $post->is_anonymous ? "Anonymous" : $userFName . " " . $userLName,
                    "userFName" => $userFName,
                    "userLName" => $userLName,
                    "caption" => $post->caption,
                    "createdAt" => $post->created_at,
                    "updatedAt" => $post->updated_at,
                    "mediaUrl" => $post->media_url,
                    "isAnonymous" => $post->is_anonymous,
                    "postType" => "university",
                    "profileUser" => $profileUser,
                    "reactionCount" => $reactionModel->countReactions($post->id),
                    "userHasReacted" => $reactionModel->getReaction($loggedInUserId, $post->id) ? true : false,
                ]);
                ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>