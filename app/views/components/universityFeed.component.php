<?php require_once(__DIR__ . "/../../models/UniversityPost.php"); ?>
<?php require_once(__DIR__ . "/../../models/User.php"); ?>

<div class="feed">
    <?php component("createPost") ?>


    <div class="global-feed">
        <?php
        $uniPostsModel = new UniversityPost();
        $uniPosts = $uniPostsModel->where([
            ["user_id", '!=', $_SESSION['user_id']],
            ["university_id", '=', $_SESSION['user_universityID']]
        ]);

        ?>

        <?php foreach ($uniPosts as $post): ?>
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
                "mediaUrl" => $post->media_url,
                "isAnonymous" => $post->is_anonymous,
            ]);
            ?>
        <?php endforeach; ?>
    </div>
    < </div>