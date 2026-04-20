<?php component("adminNavbar"); ?>

<div class="admin-users-layout">
    <?php component("adminNavpanel"); ?>

    <div class="admin-users-content">
        <?php component("usersTable", ['users' => $users]); ?>
    </div>
</div>