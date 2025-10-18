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
            <input placeholder="What's on your mind?" class="">
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