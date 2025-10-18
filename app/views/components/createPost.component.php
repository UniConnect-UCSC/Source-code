<div class="create-post">
    <div class="create-post-header">
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
                    <?= htmlspecialchars($fNameInitial) ?><?= htmlspecialchars($lNameInitial) ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="input-bar">
            <input placeholder="What's on your mind?" class="" onclick="openCreatePostModal()">
        </div>
    </div>

    <div class="post-options">
        <div>
            <i data-lucide="image"></i>
            Photo
        </div>
        <div>
            <i data-lucide="video"></i>
            Video
        </div>
    </div>

</div>


<div class="create-post-modal" id="create-post-modal" onclick="closeCreatePostModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
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
                <div>
                    <?php if ($type === "global"): ?>
                        <i data-lucide="globe" class="feed-type-svg"></i><i data-lucide="dot"></i><span>Global Feed</span>
                    <?php else: ?>
                        <i data-lucide="university" class="feed-type-svg"></i><i data-lucide="dot"></i><span>University
                            Feed</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal-post-content">
            <textarea id="post-caption"
                placeholder="What's on your mind, <?= htmlspecialchars($userFName) ?>?"></textarea>

            <!-- media picker -->
            <div class="post-media-input">
                <input id="post-media" type="file" accept="image/*,video/*" />
            </div>
        </div>

        <div class="anonymous-post-option">

            <label class="switch" id="anonymousSwitch">
                <input type="checkbox">
                <span class="slider round"></span>
            </label>

            <span class="post-anonymously">Post Anonymously</span>
        </div>

        <div class="modal-actions">
            <div class="post-options">
                <div>
                    <i data-lucide="image"></i>
                    Photo
                </div>
                <div>
                    <i data-lucide="video"></i>
                    Video
                </div>
            </div>
            <button class="create-post-button" onclick="createPost()">Post</button>
        </div>

    </div>
</div>