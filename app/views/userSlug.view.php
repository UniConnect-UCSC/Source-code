<?php component("navbar"); ?>

<div class="profile-layout">
    <?php
    component("navPanel");
    ?>
    <div class="profile-content">
        <?php component("userBanner", [
            "profileUser" => $profileUser
        ]); ?>

        <?php component("feedType"); ?>
        <?php component("profileFeed", [
            "profileUser" => $profileUser
        ]); ?>
    </div>
    <div class="profile-sidebar">
        <?php component("profileSidebar", ["profileUser" => $profileUser]); ?>
    </div>
</div>