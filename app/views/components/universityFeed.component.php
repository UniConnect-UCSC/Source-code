<?php require_once(__DIR__ . "/../../models/UniversityPost.php"); ?>
<?php require_once(__DIR__ . "/../../models/User.php"); ?>
<?php require_once(__DIR__ . "/../../models/Reaction.php"); ?>

<div class="feed">
    <?php component("createPost", ["type" => "university"]) ?>


    <div class="global-feed">
        <?php
        $uniPostsModel = new UniversityPost();
        $uniPosts = $uniPostsModel->where([
            ["university_id", '=', $_SESSION['user_universityID']],
        ], null, null, ['created_at' => 'DESC'], [], [], false);

        ?>

        <?php if (!empty($uniPosts)): ?>
            <?php foreach ($uniPosts as $post): ?>
                <?php
                $userModel = new User();
                $reactionModel = new Reaction();

                $user = $userModel->first(["id" => $post->user_id]);
                $reactionCount = $reactionModel->countReactions($post->id);
                $userHasReacted = $reactionModel->getReaction($_SESSION['user_id'], $post->id);

                component("post", [
                    "postId" => $post->id,
                    "profilePic" => $user->profile_picture ?? null,
                    "authorId" => $post->user_id,
                    "author" => $post->is_anonymous ? "Anonymous" : $user->f_name . " " . $user->l_name,
                    "userFName" => $user->f_name ?? '',
                    "userLName" => $user->l_name ?? '',
                    "caption" => $post->caption,
                    "createdAt" => $post->created_at,
                    "updatedAt" => $post->updated_at,
                    "mediaUrl" => $post->media_url,
                    "isAnonymous" => $post->is_anonymous,
                    "reactionCount" => $reactionCount,
                    "userHasReacted" => $userHasReacted ? true : false,
                    "postType" => "university",
                ]);
                ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>