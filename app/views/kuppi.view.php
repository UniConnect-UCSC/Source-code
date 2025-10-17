<?php component("navbar"); ?>

<div class="home-layout">
    <!-- Left Nav Panel -->
        <?php component("navPanel"); ?> 

    <!-- Center Feed -->
    <div class="feed">
         <div class="kuppi-feed-header">
             <h2 class="feed-title">Kuppi Sessions</h2>
             <a href="/kuppi/request_kuppi" class="btn"> Request a Kuppi</a>
             <a href="/kuppi/create_kuppi" class="btn"> Host Kuppi</a>
             <a href="/kuppi/my_kuppis" class="btn"> My Kuppies</a>
         </div>
         <div class="kuppi-feed">
         <?php
         if (!empty($testKuppi)) {
             foreach ($testKuppi as $kuppi) {
             component("kuppiPost", [
                "id" => $kuppi->id,
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

    <!-- Right Widget Panel -->
        <?php component("widgetPanel"); ?>

</div>
