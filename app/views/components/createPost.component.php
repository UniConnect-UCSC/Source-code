<div class="create-post">
    <div>
        <div class="input-bar">
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

        <div>Input bar</div>
    </div>

    <div>
        <div>Photo</div>
        <div>Tag</div>
        <div>Location</div>
    </div>

</div>