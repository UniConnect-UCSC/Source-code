<?php component("navbar"); ?>

<div class="kuppi-layout">

    <div class="Nav-panel">
        <?php component("navPanel"); ?>
    </div>

    <div class="kuppi-feed">
        <h2 class="feed-title">Kuppi Sessions</h2>

        <div class="kuppi-posts">
            <?php
            component("kuppiPost", [
                "topic" => "Introduction to Machine Learning",
                "university" => "University of Colombo School of Computing",
                "date" => "2024-06-15",
                "time" => "10:00 AM - 12:00 PM",
                "platform" => "Zoom",
                "image" => "assets/images/ml-banner.jpg",
            ]);

            component("kuppiPost", [
                "topic" => "Advanced Database Systems",
                "university" => "University of Moratuwa",
                "date" => "2024-06-20",
                "time" => "2:00 PM - 4:00 PM",
                "platform" => "Google Meet",
                "image" => "assets/images/ml-banner.jpg",
            ]);

            ?>
        </div>
    </div>

    <div class="widget-panel">
        <?php component("widgetPanel"); ?>
    </div>

</div>

