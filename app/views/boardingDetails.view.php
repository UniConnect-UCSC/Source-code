<?php component("navbar"); ?>

<?php
$locationText = 'Location not specified';
if (!empty($location)) {
    $city = $location->city ?? '';
    $district = $location->district ?? '';
    $locationText = trim($city . ', ' . $district, ', ');
}

$descriptionSafe = trim((string)($room->description ?? ''));
$facilitiesList = [];
if (!empty($room->facilities)) {
    $facilitiesList = array_filter(array_map('trim', explode(',', (string)$room->facilities)));
}

$imageCount = !empty($imageUrls) ? count($imageUrls) : 0;
?>

<div class="boardings-layout">
    

    <div class="boardings-container">
        <div class="boardings-header">
            <h2>Boarding Details</h2>
            <div class="boardings-header-buttons">
                <a href="/boardings" class="btn-myListings">Back to Listings</a>
            </div>
        </div>

        <div class="boarding-card" style="max-width: 860px; margin: 0 auto; cursor: default;">
            <div class="boarding-image details-carousel" style="height: 320px;">
                <div class="details-carousel-track" id="details-carousel-track">
                    <?php if (!empty($imageUrls)): ?>
                        <?php foreach ($imageUrls as $imageUrl): ?>
                            <img class="details-carousel-image" src="<?= htmlspecialchars($imageUrl) ?>" alt="Boarding Room" />
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="boarding-no-image details-carousel-image">No Image Available</div>
                    <?php endif; ?>
                </div>

                <?php if ($imageCount > 1): ?>
                    <button type="button" class="details-carousel-btn details-carousel-btn-prev" id="carousel-prev" aria-label="Previous image">
                        &#10094;
                    </button>
                    <button type="button" class="details-carousel-btn details-carousel-btn-next" id="carousel-next" aria-label="Next image">
                        &#10095;
                    </button>
                <?php endif; ?>
            </div>

            <div class="boarding-details">
                <p class="boarding-rent">LKR <?= number_format((float)($room->rent ?? 0)) ?>/month</p>

                <p class="boarding-occupancy">
                    <i data-lucide="users"></i>
                    <?= htmlspecialchars((string)($room->occupancy ?? 0)) ?> person<?= ((int)($room->occupancy ?? 0) > 1) ? 's' : '' ?>
                </p>

                <span class="boarding-gender <?= htmlspecialchars(strtolower((string)($room->gender ?? 'any'))) ?>">
                    <?= htmlspecialchars(ucfirst((string)($room->gender ?? 'Any'))) ?>
                </span>

                <p class="boarding-location">
                    <i data-lucide="map-pin"></i>
                    <?= htmlspecialchars($locationText) ?>
                </p>

                <?php if ($descriptionSafe !== ''): ?>
                    <p class="boarding-description boarding-description-full">
                        <?= htmlspecialchars($descriptionSafe) ?>
                    </p>
                <?php endif; ?>

                <span class="boarding-category <?= htmlspecialchars(strtolower((string)($room->category ?? 'single room'))) ?>">
                    <?= htmlspecialchars(ucfirst((string)($room->category ?? 'Single Room'))) ?>
                </span>

                <?php if (!empty($facilitiesList)): ?>
                    <div class="boarding-facilities-section">
                        <p class="boarding-facilities-title"></p>
                        <div class="boarding-facilities-list">
                            <?php foreach ($facilitiesList as $facility): ?>
                                <span class="boarding-facility-chip"><?= htmlspecialchars($facility) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($room->contact_number)): ?>
                    <div class="boarding-contact-reveal">
                        <button type="button" class="contact-reveal-btn" id="contact-reveal-btn" aria-expanded="false">
                            <i data-lucide="phone"></i>
                            Show Contact
                        </button>
                        <p class="contact-reveal-number" id="contact-reveal-number" hidden>
                            <?= htmlspecialchars((string)$room->contact_number) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <p class="boarding-date">
                    <?= htmlspecialchars(date("F j, Y", strtotime((string)($room->created_at ?? 'now')))) ?>
                </p>
            </div>
        </div>
 
        <?php if (!empty($imageUrls)): ?>
            <div class="boarding-photo-lightbox" id="boarding-photo-lightbox" hidden aria-modal="true" role="dialog" aria-label="Boarding photos viewer">
                <button type="button" class="boarding-photo-lightbox-close" id="boarding-photo-lightbox-close" aria-label="Close photo viewer">&times;</button>
                <button type="button" class="boarding-photo-lightbox-nav boarding-photo-lightbox-prev" id="boarding-photo-lightbox-prev" aria-label="Previous photo">&#10094;</button>
                <img class="boarding-photo-lightbox-image" id="boarding-photo-lightbox-image" src="" alt="Boarding photo" />
                <button type="button" class="boarding-photo-lightbox-nav boarding-photo-lightbox-next" id="boarding-photo-lightbox-next" aria-label="Next photo">&#10095;</button>
                <div class="boarding-photo-lightbox-thumbs" id="boarding-photo-lightbox-thumbs" aria-label="Photo thumbnails"></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="/assets/js/boardingDetails.js"></script>
