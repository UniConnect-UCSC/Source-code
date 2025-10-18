<div class="post">
    <div class="post-header">
        <div class="post-author">

            <!-- User Profile Picture -->
            <div class="profile-section">
                <?php
                $userEmail = $_SESSION['user_email'] ?? "";
                $userFName = $_SESSION['user_fName'] ?? "";
                $userLName = $_SESSION['user_lName'] ?? "";
                $profilePic = $_SESSION['user_profilePicture'] ?? null;
                ?>
                <?php if ($profilePic): ?>
                <a href="/profile">
                    <img class="profile-image" src="<?= htmlspecialchars($profilePic) ?>">
                </a>
                <?php else: ?>
                <?php
                    $fNameInitial = strtoupper($userFName[0] ?? '');
                    $lNameInitial = strtoupper($userLName[0] ?? '');
                    ?>
                <a class="profile" href="/profile">
                    <!-- //Is isAnonymous do not display initials -->
                    <?= htmlspecialchars($isAnonymous ? 'An' : $fNameInitial) ?><?= htmlspecialchars($isAnonymous ? '' : $lNameInitial) ?>
                </a>
                <?php endif; ?>
            </div>

            <div>

                <p><?= htmlspecialchars($author) ?></p>
                <span>Posted <?= htmlspecialchars(timeAgo($createdAt)) ?></span>
            </div>
        </div>

        <div class="post-more-options">
            <i data-lucide="more-horizontal" onclick="toggleOptionsMenu(this)"></i>

            <div class="post-more-options-menu" data-post-id="<?= htmlspecialchars($postId) ?>">
                <!-- Optionally render if the post belongs to the user -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $authorId): ?>
                <div>Edit Post</div>
                <div class="delete-post-btn" onclick="deletePost(this)">Delete Post</div>
                <?php endif; ?>

                <div>Copy URL</div>

                <!-- Optionally Render if post doesnt belong to user -->
                <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $authorId): ?>
                <div>Report Post</div>
                <?php endif; ?>


            </div>
        </div>
    </div>

    <div class="post-caption"><?= htmlspecialchars($caption) ?>
    </div>

    <div class="post-image">
        <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="Post Media">
    </div>

    <div class="post-interactions">
        <div><i data-lucide="thumbs-up"></i> 4</div>
        <div class="interaction-counts">
            <div>5 Comments</div>
            <i data-lucide="dot"></i>
            <div>43 Reposts</div>
        </div>
    </div>

    <div class="post-actions">
        <div> <i data-lucide="thumbs-up"></i>Like</div>
        <div><i data-lucide="message-circle"></i>Comment</div>
        <div><i data-lucide="repeat-2"></i>Repost</div>
    </div>
</div>