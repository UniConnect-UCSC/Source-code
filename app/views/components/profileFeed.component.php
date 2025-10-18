<?php require_once(__DIR__ . "/../../models/GlobalPost.php"); ?>
<div class="profile-feed">
    <?php
    $postsModel = new GlobalPost();
    $posts = $postsModel->where(["user_id" => $_SESSION['user_id']]);
    ?>

    <?php foreach ($posts as $post): ?>
        <?php
        component("post", [
            "postId" => $post->id,
            "authorId" => $post->user_id,
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