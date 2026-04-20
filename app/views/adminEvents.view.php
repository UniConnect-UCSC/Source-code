<?php component("adminNavbar"); ?>

<div class="admin-posts-layout">
    <?php component("adminNavpanel"); ?>

    <div class="admin-posts-content">
        <?php component("eventsTable", ['events' => $events]); ?>
    </div>
</div>