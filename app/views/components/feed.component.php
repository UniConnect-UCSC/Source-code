<?php require_once(__DIR__ . "/../../models/GlobalPost.php"); ?>
<?php require_once(__DIR__ . "/../../models/User.php"); ?>

<div class="feed">
    <?php component("createPost", ["type" => "global"]) ?>


    <div class="global-feed">
        <?php
        $postsModel = new GlobalPost();
        $posts = $postsModel->where([], null, null, ['created_at' => 'DESC']);
        ?>

        <?php foreach ($posts as $post): ?>
            <?php
            $userModel = new User();
            $user = $userModel->first(["id" => $post->user_id]);

            component("post", [
                "postId" => $post->id,
                "authorId" => $post->user_id,
                "author" => $post->is_anonymous ? "Anonymous" : $user->f_name . " " . $user->l_name,
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
</div>