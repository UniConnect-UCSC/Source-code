
<script>
    const kuppiCategories = <?= json_encode($kuppiCategories) ?>;
</script>
<script src="/assets/js/kuppiModal.js"></script>
<?php component("navbar"); ?>
<div class="home-layout">
    <?php component("navPanel"); ?>
    <div class="feed">

        <div class="kuppi-feed-header">
            <h2>My Kuppis</h2>  
        </div>
        <div class="kuppi-feed">
            <?php
            if (!empty($myKuppies)) {
                foreach ($myKuppies as $kuppi) {
            ?>
                <div class="kuppi-post" data-id="<?=htmlspecialchars($id ?? $kuppi->id) ?>"
                onclick="openKuppiModal(this)">
                    <?php
                    component("kuppiPost", [
                        "topic" => $kuppi->topic,
                        "university" => $kuppi->university,
                        "host_id" => $kuppi->host_id,
                        "requester_id" => $kuppi->requester_id,
                        "category" => $kuppi->category,
                        "status" => $kuppi->status,
                        "date" => !empty($kuppi->kuppi_date_time) ? date('Y-m-d', strtotime($kuppi->kuppi_date_time)) : '',
                        "time" => !empty($kuppi->kuppi_date_time) ? date('H:i', strtotime($kuppi->kuppi_date_time)) : '',
                        "platform" => $kuppi->platform,
                        "image" => empty($kuppi->image_url) ? 'assets/images/ml-banner.jpg' : $kuppi->image_url,
                    ]);
                    ?>
                </div>
            <?php
                }
            } else {
            ?>
                <div class="no-kuppi-requests"><p>No Kuppi sessions found.</p></div>
            <?php
            }
            ?>
        </div>
        <div id="kuppiModal" class="kuppi-modal-overlay" style="display:none;">
            <div class="kuppi-modal-content">
                <button class="kuppi-modal-close" onclick="closeKuppiModal()">&times;</button>
                <div id="kuppiModalBody"></div>
            </div>
        </div>
    </div>
    <?php component("widgetPanel"); ?>
</div>
