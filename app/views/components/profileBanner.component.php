<?php require_once(__DIR__ . "/../../models/University.php"); ?>
<?php require_once(__DIR__ . "/../../models/User.php"); ?>

<div class="profile-banner">
    <?php
    $userFName = $_SESSION['user_fName'] ?? "";
    $userLName = $_SESSION['user_lName'] ?? "";
    $profilePic = $_SESSION['user_profilePicture'] ?? null;

    $userModel = new User();
    $user = $userModel->first(["id" => $_SESSION['user_id']]);

    $universityModel = new University();
    $userUniversity = $universityModel->first(["id" => $_SESSION['user_universityID']]);
    $universityName = $userUniversity->name ?? "Unknown University";
    ?>

    <div class="banner-image"></div>
    <div class="profile-section">
        <div class="user-profile-picture">
            <?php if ($profilePic): ?>
            <a href="/profile">
                <img class="banner-profile-image" src="<?= htmlspecialchars($profilePic) ?>">
            </a>
            <?php else: ?>
            <?php
                $fNameInitial = strtoupper($userFName[0] ?? '');
                $lNameInitial = strtoupper($userLName[0] ?? '');
                ?>
            <a class="banner-profile" href="/profile">
                <?= htmlspecialchars($fNameInitial) ?><?= htmlspecialchars($lNameInitial) ?>
            </a>
            <?php endif; ?>
        </div>

        <div class="user-about">
            <h2 class=""><?= htmlspecialchars($userFName . ' ' . $userLName) ?></h2>
            <!-- //if bio exists -->
            <?php if (!empty($user->bio)): ?>
            <p class="user-bio"><?= htmlspecialchars($user->bio) ?></p>
            <?php endif; ?>

            <div class="user-university-info">
                <i data-lucide="university"></i>
                <span class="user-university"><?= htmlspecialchars($universityName) ?></span>
            </div>

        </div>
    </div>
</div>