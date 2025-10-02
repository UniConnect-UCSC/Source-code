<?php require(__DIR__ . "/../../core/utils.php"); ?>
<?php require(__DIR__ . "/../../models/User.php"); ?>

<nav class="navbar">
    <a class="image__container-" href="/">
        <img src="/assets/images/logo.svg" width="150">
    </a>

    <!-- <div class="links__container">
        <?php
        foreach ($navbarLinks as $link) {
            echo '<span>';
            echo '<a href="' . htmlspecialchars($link["pageLink"]) . '">';
            echo htmlspecialchars($link["pageName"]);
            echo '</a>';
            echo '</span>';
        }
        ?>
    </div> -->

    <div class="profile__container">


        <!-- Search Bar -->
        <div class="search-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input placeholder="Search Uniconnect" class="search">
        </div>

        <!-- Notifications -->
        <div class="bell__container">
            <i data-lucide="bell"></i>
        </div>

        <!-- User Icon -->
        <?php
<<<<<<< HEAD
        $userModel = new User();

        $userEmail = $_SESSION['user_email'] ?? "";
        $user = $userModel->first(["email" => $userEmail]);

        $profilePic = $user->profile_picture ?? null;
=======

        $userEmail = $_SESSION['user_email'] ?? "";
        $userFName = $_SESSION['user_fName'] ?? "";
        $userLName = $_SESSION['user_lName'] ?? "";
        $profilePic = $_SESSION['user_profilePicture'] ?? null;
>>>>>>> 6511c0bd6573beafcd7535feb9ae75f99e263f5b
        ?>

        <?php if ($profilePic): ?>
        <a href="/profile">
            <img class="profile-image" src="<?= htmlspecialchars($profilePic) ?>">
        </a>
        <?php else: ?>
        <?php
<<<<<<< HEAD
            $fNameInitial = strtoupper($user->f_name[0] ?? '');
            $lNameInitial = strtoupper($user->l_name[0] ?? '');
=======
            $fNameInitial = strtoupper($userFName[0] ?? '');
            $lNameInitial = strtoupper($userLName[0] ?? '');
>>>>>>> 6511c0bd6573beafcd7535feb9ae75f99e263f5b
            ?>
        <a class="profile" href="/profile">
            <?= htmlspecialchars($fNameInitial) ?><?= htmlspecialchars($lNameInitial) ?>
        </a>
        <?php endif; ?>
    </div>
</nav>