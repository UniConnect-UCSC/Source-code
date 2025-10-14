<?php component("navbar"); ?>
<div class="studyMaterial-layout">

    <!-- Left Nav Panel -->
    <div class="Nav-panel">
        <?php component("navPanel"); ?> 
    </div>

    <!-- Center Feed -->
    <div class="studyMaterial-feed">
        <!-- Feed Title -->
        <h2 class="feed-title">Igena ganin nathn palayan</h2>

        <!-- Posts -->
        <?php
        component("studyMaterial", [
            "topic" => "Introduction to Machine Learning",
            "university" => "University of Colombo School of Computing",
            "views" => "1K Views",
            "type" => "Document",
            "image" => "assets/images/ml-banner.jpg",
        ]);

        ?>
    </div>

    <!-- Right Widget Panel -->
    <div class="widget-panel">
        <?php component("widgetPanel"); ?>
    </div>

</div>
