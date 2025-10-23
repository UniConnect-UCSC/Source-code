
<script>
    const kuppiCategories = <?= json_encode($kuppiCategories) ?>;
</script>
<script src="/assets/js/kuppi.js"></script>
<?php component("navbar"); ?>

<div class="home-layout">
    <!-- Left Nav Panel -->
        <?php component("navPanel"); ?> 

    <!-- Center Feed -->
    <div class="feed">
        <div class="kuppi-feed-header">
                <h2 class="feed-title">Kuppi Sessions</h2>
        </div>
        <div class="kuppi-feed-actions">
            <a href="javascript:void(0);" class="btn" onclick="openRequestKuppiModal()">Request a Kuppi</a>
            <a href="javascript:void(0);" class="btn" onclick="openHostKuppiModal(kuppiCategories)">Host Kuppi</a>
            <a href="/kuppi/my_kuppis" class="btn">My Kuppies</a>
            <a href="/kuppi/kuppi_requests" class="btn">Kuppi Requests</a>
        </div>
        <div class="kuppi-feed" data-id="<?= htmlspecialchars($id ?? '') ?>"
                    onclick="openKuppiModal(this)">

            <?php
            if (!empty($testKuppi)) {
             foreach ($testKuppi as $kuppi) {
             component("kuppiPost", [
                "host_name" => $kuppi->host_name,

                "id" => $kuppi->id,
                 "topic" => $kuppi->topic,
                 "university" => $kuppi->university ?? 'Unknown University',
                 "date" => date('Y-m-d', strtotime($kuppi->kuppi_date_time)),
                 "time" => date('H:i', strtotime($kuppi->kuppi_date_time)),
                 "category" => $kuppi->category,
                 "platform" => $kuppi->platform,
                 "image" => $kuppi->image_url,
                 "context" => "main"
             ]);
             }
         }     
         ?>

        </div>
    </div>


         <?php component("widgetPanel"); ?>
        <div id="kuppiModal" class="kuppi-modal-overlay" style="display:none;">
            <div class="kuppi-modal-content">
                <button class="kuppi-modal-close" onclick="closeKuppiModal()">&times;</button>
            <div id="kuppiModalBody"></div>
        </div>


    <!-- Right Widget Panel -->

</div>
