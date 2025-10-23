<?php component("navbar"); ?>
<div class="home-layout">
    <?php component("navPanel"); ?>
    <div class="feed">
        <div class="kuppi-feed-header">
            <h2 class="feed-title">Kuppi Requests</h2>
        </div>
        <div class="kuppi-feed">
            <?php if (!empty($kuppiRequests)) { 
                foreach ($kuppiRequests as $kuppi) { ?>
                    <div class="kuppi-post-container"
                         data-id="<?= htmlspecialchars($kuppi->id) ?>"
                         data-topic="<?= htmlspecialchars($kuppi->topic, ENT_QUOTES) ?>"
                         data-category="<?= htmlspecialchars($kuppi->category, ENT_QUOTES) ?>"
                         data-requester-name="<?= htmlspecialchars($kuppi->requester_name, ENT_QUOTES) ?>"
                         data-university="<?= htmlspecialchars($kuppi->requester_university, ENT_QUOTES) ?>"
                         onclick="openKuppiRequestModal(this)">
                        <?php component("kuppiPost", [
                            "id" => $kuppi->id,
                            "topic" => $kuppi->topic,
                            "category" => $kuppi->category,
                            "status" => $kuppi->status,
                            "requester_name" => $kuppi->requester_name,
                            "university" => $kuppi->requester_university,
                            "context" => "kuppi_requests"
                        ]); ?>
                    </div>
            <?php } 
            } else { ?>
                <div class="no-kuppi-requests"><p>No Kuppi requests found.</p></div>
            <?php } ?>
        </div>
    </div>
    <?php component("widgetPanel"); ?>
</div>

<!-- Modal markup (must exist once on the page) -->
<div id="kuppiModal" class="kuppi-modal-overlay" style="display:none;">
  <div class="kuppi-modal-content">
    <button class="kuppi-modal-close" onclick="closeKuppiModal()">&times;</button>
    <div id="kuppiModalBody"></div>
  </div>
</div>

<script>const kuppiCategories = <?= json_encode($kuppiCategories ?? []) ?>;</script>
<script src="/assets/js/kuppi.js"></script>
