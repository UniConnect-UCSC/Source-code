<?php component("navbar"); ?>
<div class="home-layout">

    <?php component("navPanel"); ?>
    <div class="feed">
        <div class="kuppi-feed">
            <?php
                 if (!empty($myKuppies)) {
                foreach ($myKuppies as $kuppi) {
                component("kuppiPost", [
                    "topic" => $kuppi->topic,
                    "university" => $kuppi->university_id, // Replace with university name if available
                    "date" => date('Y-m-d', strtotime($kuppi->kuppi_date_time)),
                    "time" => date('H:i', strtotime($kuppi->kuppi_date_time)),
                    "platform" => $kuppi->platform,
                    "image" => $kuppi->image_url,
                ]);
                }
            }     
            ?>
        </div>
    </div>
        <?php component("widgetPanel"); ?>
</div>
