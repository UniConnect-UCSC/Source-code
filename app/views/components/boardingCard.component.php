<?php
require_once(__DIR__ . "/../../models/BoardingRoomImage.php");

// Get location details if location_id is provided
$locationModel = new Location();
$location = null;
$locationText = 'Location not specified';

if (isset($location_id) && $location_id) {
    $location = $locationModel->first(['id' => $location_id]);
    if ($location) {
        $locationText = htmlspecialchars($location->city) . ',' . htmlspecialchars($location->district);
    }
}

// Handle image URL
$imageUrl = $image_url ?? null;

$imageModel = new BoardingRoomImage();
$roomImages = $imageModel->where([['room_id', '=', $id]], null, null, ['id' => 'ASC']);
$imageUrls = [];

if (!empty($roomImages)) {
    foreach ($roomImages as $roomImage) {
        if (!empty($roomImage->img_url)) {
            $imageUrls[] = $roomImage->img_url;
        }
    }
}

if (empty($imageUrls) && !empty($imageUrl)) {
    $imageUrls[] = $imageUrl;
}

$facilitiesList = [];
if (!empty($facilities)) {
    $facilitiesList = array_filter(array_map('trim', explode(',', (string)$facilities)));
}



?>

<div class="boarding-card" data-room-id="<?= htmlspecialchars($id) ?>">
    <div class="boarding-image">
        <?php if (!empty($imageUrls)): ?>
            <img src="<?= htmlspecialchars($imageUrls[0]) ?>" alt="Boarding Room" />
        <?php else: ?>
            <div class="boarding-no-image">No Image Available</div>
        <?php endif; ?>
    </div>

    <div class="boarding-details">
        <p class="boarding-rent">LKR <?= number_format(htmlspecialchars($rent)) ?>/month</p>

        <p class="boarding-occupancy">
            <i data-lucide="users"></i>
            <?= htmlspecialchars($occupancy) ?> person<?= $occupancy > 1 ? 's' : '' ?>
        </p>

        <span class="boarding-gender <?= strtolower($gender) ?>">
            <?= htmlspecialchars(ucfirst($gender ?? 'male students')) ?>
        </span>

        <p class="boarding-location">
            <i data-lucide="map-pin"></i>
            <?= $locationText ?>
        </p>

        <span class="boarding-category <?= strtolower($category) ?>">
            <?= htmlspecialchars(ucfirst($category ?? 'single Room')) ?>
        </span>

        <?php if (!empty($facilitiesList)): ?>
            <div class="boarding-facilities-list">
                <?php foreach ($facilitiesList as $facility): ?>
                    <span class="boarding-facility-chip"><?= htmlspecialchars($facility) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <span class="boarding-status <?= strtolower($status) ?>">
            <?= htmlspecialchars(ucfirst($status ?? 'available')) ?>
        </span>


        <p class="boarding-date">
            <?= htmlspecialchars(date("F j, Y", strtotime($created_at ?? ''))) ?>
        </p>
    </div>

    <?php if (isset($myListings) && $myListings): ?>
        <div class="my-listings-options"
            onclick="event.stopPropagation(); toggleMyListingsOptions('<?= htmlspecialchars($id) ?>')">
            <i data-lucide="more-horizontal"></i>
        </div>
        <div class="my-listings-options-dropdown" id="my-listings-options-dropdown-<?= htmlspecialchars($id) ?>">
            <div class="edit-listing-btn" data-room-id="<?= htmlspecialchars($id) ?>">Edit</div>
            <div class="delete-listing-btn" data-room-id="<?= htmlspecialchars($id) ?>">Delete</div>
        </div>
    <?php endif; ?>
</div>