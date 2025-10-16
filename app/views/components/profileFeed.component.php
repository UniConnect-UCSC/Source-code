<?php require_once(__DIR__ . "/../../models/Post.php"); ?>
<div class="profile-feed">
    <?php
    $postsModel = new Post();
    $posts = $postsModel->where(["user_id" => $_SESSION['user_id']]);
    ?>

    <?php foreach ($posts as $post): ?>
        <?php
        component("post", [
            "postId" => $post->id,
            "author" => $post->is_anonymous ? "Anonymous" : $_SESSION['user_fName'] . " " . $_SESSION['user_lName'],
            "caption" => $post->caption,
            "createdAt" => $post->created_at,
            "updatedAt" => $post->updated_at,
            "groupID" => $post->group_id,
            "mediaUrl" => $post->media_url,
            "isAnonymous" => $post->is_anonymous,
        ]);
        ?>
    <?php endforeach; ?>
</div>