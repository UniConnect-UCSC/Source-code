<?php component("navbar"); ?>

<div class="kuppi-layout">

    <!-- Left Nav Panel -->
    <div class="Nav-panel">
        <?php component("navPanel"); ?> 
    </div>

    <!-- Center Feed -->
    <div class="kuppi-feed">
        <!-- Feed Title -->
        <h2 class="feed-title">Kuppi Sessions</h2>

        <!-- Posts -->
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
        component("kuppiPost", [
            "topic" => "Cybersecurity Essentials",
            "university" => "Sri Lanka Institute of Information Technology",
            "date" => "2024-06-25",
            "time" => "11:00 AM - 1:00 PM",
            "platform" => "Microsoft Teams",
            "image" => "assets/images/ml-banner.jpg",
        ]);
        component("kuppiPost", [
            "topic" => "Cloud Computing Basics",
            "university" => "University of Ruhuna",
            "date" => "2024-06-30",
            "time" => "3:00 PM - 5:00 PM",
            "platform" => "Zoom",
            "image" => "assets/images/ml-banner.jpg",
        ]);
        component("kuppiPost", [
            "topic" => "Data Science Workshop",
            "university" => "University of Peradeniya",
            "date" => "2024-07-05",
            "time" => "9:00 AM - 11:00 AM",
            "platform" => "Google Meet",
            "image" => "assets/images/ml-banner.jpg",
        ]);
        component("kuppiPost", [
            "topic" => "Data Science Workshop",
            "university" => "University of Peradeniya",
            "date" => "2024-07-05",
            "time" => "9:00 AM - 11:00 AM",
            "platform" => "Google Meet",
            "image" => "assets/images/ml-banner.jpg",
        ]);
        
        ?>
    </div>

    <!-- Right Widget Panel -->
    <div class="widget-panel">
        <?php component("widgetPanel"); ?>
    </div>

</div>
