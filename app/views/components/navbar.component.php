<?php require(__DIR__ . "/../../core/utils.php"); ?>
<?php require(__DIR__ . "/../../models/User.php"); ?>

<nav class="navbar">
    <a class="image__container-" href="/">
        <img src="/assets/images/logo.svg" width="150">
    </a>

    <div class="links__container">
        <?php
        foreach ($navbarLinks as $link) {
            echo '<span>';
            echo '<a href="' . htmlspecialchars($link["pageLink"]) . '">';
            echo htmlspecialchars($link["pageName"]);
            echo '</a>';
            echo '</span>';
        }
        ?>
    </div>

    <div class="profile__container">


        <?php
        $userModel = new User();

        $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : "";

        $user = $userModel->first(["email" => $userEmail]);

        $profilePic = $user->profile_picture;

        if ($profilePic) {
            echo ('<a href="/profile">');
            echo ('<img class="profile-image" src=' . $profilePic . '>');
            echo ("</a>");
        } else {
            $fNameInitial = strtoupper($user->f_name[0]);
            $lNameInitial = strtoupper($user->l_name[0]);

            echo ('<a class="profile" href="/profile">');
            echo (htmlspecialchars($fNameInitial));
            echo (htmlspecialchars($lNameInitial));
            echo ("</a>");
        }


        ?>
        </a>
    </div>
</nav>