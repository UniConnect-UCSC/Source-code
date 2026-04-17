<?php require_once(__DIR__ . "/../../core/utils.php"); ?>
<?php require_once(__DIR__ . "/../../models/User.php"); ?>

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

    <div class="navigation__container">


        <!-- Search Bar -->
        <form class="search-wrapper" action="/search" method="GET" role="search">
            <i data-lucide="search" class="search-icon" aria-hidden="true"></i>
            <input name="q" placeholder="Search Uniconnect" class="search" aria-label="Search" />
        </form>

        <!-- Notifications -->
        <?php component('notification'); ?>

        <a href="/calendar">
            <i data-lucide="calendar"></i>
        </a>

        <!-- User Icon -->
        <?php
        $userModel = new User();

        $userEmail = $_SESSION['user_email'] ?? "";
        $user = $userModel->first(["email" => $userEmail]);

        // $profilePic = $user->profile_picture ?? null;
        $profilePic = null;


        $userEmail = $_SESSION['user_email'] ?? "";
        $userFName = $_SESSION['user_fName'] ?? "";
        $userLName = $_SESSION['user_lName'] ?? "";
        $profilePic = $_SESSION['user_profilePicture'] ?? null;
        ?>
        <div class="profile__container">
            <?php if ($profilePic): ?>
                <div>
                    <img class="profile-image" src="<?= htmlspecialchars($profilePic) ?>">
                </div>
            <?php else: ?>
                <?php

                $fNameInitial = strtoupper($user->f_name[0] ?? '');
                $lNameInitial = strtoupper($user->l_name[0] ?? '');

                $fNameInitial = strtoupper($userFName[0] ?? '');
                $lNameInitial = strtoupper($userLName[0] ?? '');
                ?>
                <div class="profile">
                    <?= htmlspecialchars($fNameInitial) ?><?= htmlspecialchars($lNameInitial) ?>
                </div>
            <?php endif; ?>

            <!-- <i data-lucide="chevron-down" class="chevron-down"></i> -->


            <div class="profile__content" id="user-content">
                <a href="/profile" class="user__profile">
                    <?php if ($profilePic): ?>
                        <img class="profile-image" src="<?= htmlspecialchars($profilePic) ?>">
                    <?php else: ?>
                        <span class="profile">
                            <?= htmlspecialchars($fNameInitial) ?><?= htmlspecialchars($lNameInitial) ?>
                        </span>
                    <?php endif; ?>
                    <span>
                        <?= htmlspecialchars($userFName) ?> <?= htmlspecialchars($userLName) ?>
                    </span>
                </a>

                <a href="/settings" class="user__settings">
                    <i data-lucide="settings"></i>
                    <span>Settings</span>
                </a>

                <a class="user__logout" href="/logout">
                    <i data-lucide="log-out"></i>
                    <span>Logout</span>
                </a>
            </div>

        </div>

    </div>
</nav>