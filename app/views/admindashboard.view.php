<?php component("adminNavbar"); ?>

<div class="admin-dashboard-layout">
    <?php
    component("adminNavpanel");
    ?>

    <div class="admin-dashboard-content">
        <div class="dashboard-heading">
            <h1>Hello, Admin!</h1>
            <p>Welcome to the Admin Dashboard!</p>
        </div>

        <div class="stat-card-container">
            <?php component("statcard", [
                'title' => 'Total Users',
                'value' => '...',
                'valueId' => 'total-users-value'
            ]); ?>

            <?php component("statcard", [
                'title' => 'Global Posts',
                'value' => '...',
                'valueId' => 'total-global-posts-value'
            ]); ?>

            <?php component("statcard", [
                'title' => 'University Posts',
                'value' => '...',
                'valueId' => 'total-university-posts-value'
            ]); ?>

            <!-- <?php component("statcard", ['title' => 'Reported Posts', 'value' => '12']);    ?> -->


            <?php component("statcard", ['title' => 'Total Comments', 'value' => '...', 'valueId' => 'total-comments-value']); ?>

            <?php component("statcard", ['title' => 'Total Universities', 'value' => '...', 'valueId' => 'total-universities-value']);    ?>

            <!-- <?php component("statcard", ['title' =>
                    "Kuppi's posted", 'value' => '3,210']);    ?>

            <?php component("statcard", ['title' => "Items Listed", 'value' => '6,543']);    ?>

            <?php component("statcard", ['title' => "Events Created", 'value' => '89']);    ?> -->
        </div>
    </div>
</div>