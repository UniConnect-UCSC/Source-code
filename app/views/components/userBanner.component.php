<?php require_once(__DIR__ . "/../../models/University.php"); ?>
<!-- <?php require_once(__DIR__ . "/../../models/User.php"); ?> -->

<div class="profile-banner">
    <?php

    $userFName =  $profileUser->f_name ?? "";
    $userLName = $profileUser->l_name ?? "";
    $profilePic = $profileUser->profile_picture ?? null;
    $userId = $profileUser->id ?? null;


    $universityModel = new University();
    $userUniversity = $universityModel->first(["id" => $profileUser->university_id]);
    $universityName = $userUniversity->name ?? "Unknown University";
    ?>

    <div class="banner-image"></div>
    <div class="profile-section">
        <div class="user-profile-picture">
            <?php if ($profilePic): ?>

                <img class="banner-profile-image" src="<?= htmlspecialchars($profilePic) ?>">

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
            <?php if (!empty($profileUser->bio)): ?>
                <p class="user-bio"><?= htmlspecialchars($profileUser->bio) ?></p>
            <?php endif; ?>

            <div class="user-university-info">
                <i data-lucide="university"></i>
                <span class="user-university"><?= htmlspecialchars($universityName) ?></span>
            </div>

        </div>


    </div>

    <?php
    if ($userId && $userId != ($_SESSION['user_id'] ?? null)) {
        component("userFriendActions", [
            "profileUser" => $profileUser,
            "relationshipState" => $relationshipState ?? 'none'
        ]);
    }
    ?>
</div>