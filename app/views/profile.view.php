<?php component("navbar"); ?>

<div class="profile-layout">
    <?php
    component("navPanel");
    ?>
    <div class="profile-content">
        <?php component("profileBanner"); ?>
        <?php component("createPost"); ?>
        <?php component("feedType"); ?>
        <?php component("profileFeed"); ?>
    </div>
    <div class="profile-sidebar">
        <?php component("profileSidebar"); ?>
    </div>
</div>