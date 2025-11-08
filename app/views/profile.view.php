<<<<<<< HEAD
<?php component("navbar"); ?>

<div class="profile-layout">
    <?php
    component("navPanel");
    ?>
    <div class="profile-content">
        <?php component("profileBanner"); ?>

        <?php component("feedType"); ?>
        <?php component("profileFeed"); ?>
    </div>
    <div class="profile-sidebar">
        <?php component("profileSidebar"); ?>
    </div>
</div>
=======
<?php
component("navbar"); ?>
<p style="margin: 80px 40px;">Click here to logout</p>
<a style="margin: 40px; background-color:red; color:white; padding: 4px 10px;" href="/logout">Logout</a>
>>>>>>> main
