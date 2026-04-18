<?php
if (!isset($postType)) {
    $postType = "";
}
?>

<div class="post">
    <div class="post-header">
        <div class="post-author">

            <!-- User Profile Picture -->
            <div class="profile-section">
                <?php
                ?>
                <?php if ($profilePic && !$isAnonymous): ?>
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
                <span class="post-time">Posted <?= htmlspecialchars(timeAgo($createdAt)) ?></span>
            </div>
        </div>

        <div class="post-more-options">
            <i data-lucide="more-horizontal" class="more-options-icon" onclick="toggleOptionsMenu(this)"></i>

            <div class="post-more-options-menu" data-post-id="<?= htmlspecialchars($postId) ?>">

                <!-- Optionally render if the post belongs to the user -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $authorId): ?>
                    <div onclick="openEditPostModal('<?= htmlspecialchars($mediaUrl ?? '') ?>')">Edit Post</div>
                    <div class="delete-post-btn" onclick="deletePost(this, event, '<?= htmlspecialchars($postType) ?>')">
                        Delete Post</div>
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

    <?php if (isset($mediaUrl) && !empty($mediaUrl)): ?>
        <div class="post-image">
            <img src="<?= htmlspecialchars($mediaUrl) ?>" alt="Post Media">
        </div>
    <?php endif; ?>

    <div class="post-interactions">
        <!-- <div class="reaction-count" data-post-id="<?= htmlspecialchars($postId) ?>">
            <i data-lucide="thumbs-up"></i> <?= htmlspecialchars($reactionCount) ?>
        </div> -->
        <div class="interaction-counts">
            <!-- <div>5 Comments</div> -->


            <!-- <i data-lucide="dot"></i>
            <div>43 Reposts</div> -->
        </div>
    </div>

    <div class="post-actions">
        <button class="like-btn <?= $userHasReacted ? 'liked' : '' ?>" data-post-id="<?= htmlspecialchars($postId) ?>"
            onclick="reactOnPost(<?= htmlspecialchars($postId) ?>, '<?= htmlspecialchars($postType) ?>', this)">
            <i data-lucide="thumbs-up"></i> <?= htmlspecialchars($reactionCount) ?>
        </button>

        <button onclick="openCommentPanel(<?= htmlspecialchars($postId) ?>)">
            <i data-lucide="message-circle"></i> Comment
        </button>


    </div>
</div>

<!-- Edit Post Modal -->

<div class="edit-post-modal" onclick="closeEditPostModal()">
    <div class="edit-modal-content" onclick="event.stopPropagation()">

        <div class="loading-spinner" id="edit-post-loading-spinner" style="display:none;">
            <div class="spinner"></div>
        </div>

        <div class="edit-modal-header">
            <div>
                <?php if ($profilePic): ?>
                    <a href="/profile">
                        <img class="profile-image" src="<?= htmlspecialchars($profilePic) ?>">
                    </a>
                <?php else: ?>
                    <a class="profile" href="/profile">
                        <?= htmlspecialchars($fNameInitial) ?><?= htmlspecialchars($lNameInitial) ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="user-info">
                <p><?= htmlspecialchars($userFName) ?> <?= htmlspecialchars($userLName) ?></p>
                <span class="post-time"><?= htmlspecialchars(timeAgo($createdAt)) ?></span>
            </div>
        </div>

        <div class="">
            <div class="edit-post-caption">
                <textarea id="edit-post-caption"><?= htmlspecialchars($caption) ?></textarea>
            </div>

            <div class="anonymous-edit-post-option">
                <label class="switch" id="edit-anonymousSwitch">
                    <input type="checkbox">
                    <span class="slider round"></span>
                </label>

                <span class="post-anonymously">Post Anonymously</span>
            </div>
            <div class="edit-post-image" <?= empty($mediaUrl) ? 'style="display:none;"' : '' ?>>
                <img id="edit-preview-img" class="edit-preview-img" src="<?= htmlspecialchars($mediaUrl ?? '') ?>"
                    alt="Post Media">
            </div>

            <div class="modal-actions">

                <!-- media picker -->
                <div class="edit-post-media-input">
                    <input id="edit-media" type="file" accept="image/*" />
                </div>

                <div class="post-options">
                    <div onclick="openEditImageSelector()">
                        <i data-lucide="image"></i>
                        Photo
                    </div>
                    <div onclick="openEditImageSelector()">
                        <i data-lucide="video"></i>
                        Video
                    </div>
                </div>
                <button class="create-post-button"
                    onclick="editPost(<?= htmlspecialchars($postId) ?>, '<?= htmlspecialchars($postType) ?>')">Post</button>
            </div>
        </div>
    </div>
</div>