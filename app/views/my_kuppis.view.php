<script>
    const kuppiCategories = <?= json_encode($kuppiCategories) ?>;
</script>
<script src="/assets/js/kuppi.js"></script>
<?php component("navbar"); ?>
<div class="home-layout">
    <?php component("navPanel"); ?>
    <div class="feed">
        <div class="kuppi-feed-header">
            <h2 class="feed-title">My Requests and Hosts</h2>
        </div>
        <div class="kuppi-feed-actions" style="position: relative;">
            <button id="filterBtn" class="filter-btn">
                <span class="material-icons" style="vertical-align:middle;">filter_list</span> Filter
            </button>
            <div id="filterDropdown" class="filter-dropdown" style="display:none; position:absolute; top:48px; right:0; z-index:10;">
                <select id="statusFilter" class="filter-select" style="width:140px;">
                    <option value="">All Statuses</option>
                    <option value="Requested">Requested</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
        </div>
        <div class="kuppi-feed">
            <?php
            if (!empty($myKuppies)) {
                foreach ($myKuppies as $kuppi) {
            ?>
                <div class="kuppi-post-container">
                    <?php
                    component("kuppiPost", [
                        "id" => $kuppi->id,
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
                        "context" => "my_kuppis"
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
        </div>
        <div id="kuppiModal" class="kuppi-modal-overlay" style="display:none;">
            <div class="kuppi-modal-content">
                <button class="kuppi-modal-close" onclick="closeKuppiModal()">&times;</button>
                <div id="kuppiModalBody"></div>
            </div>
        </div>

    </div>
