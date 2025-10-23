<?php
require_once(__DIR__ . "/../../models/MarketplaceItemImage.php");

$imageModel = new MarketplaceItemImage();
$image = $imageModel->first(['marketplace_item_id' => $id]);
$imageUrl = $image && isset($image->image_url) ? $image->image_url : null;
?>

<div class="marketplace-item-card">
    <div class="marketplace-item-image">
        <?php if (!empty($imageUrl)): ?>
        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($title ?? '') ?>" />
        <?php else: ?>
        <div class="marketplace-item-no-image">No Image Available</div>
        <?php endif; ?>
    </div>

    <div class="marketplace-item-details">
        <h3 class="marketplace-item-title"><?= htmlspecialchars($title) ?></h3>
        <p class="marketplace-item-price">LKR <?= htmlspecialchars($price) ?></p>
        <span class="marketplace-item-status <?= strtolower($status) ?>">
            <?= htmlspecialchars($status ?? 'Available') ?>
        </span>

        <p class="marketplace-item-date">
            <?= htmlspecialchars(date("F j, Y", strtotime($created_at ?? ''))) ?>
        </p>
    </div>

    <?php if (isset($myItems) && $myItems): ?>
    <div class="my-items-options"
        onclick="event.stopPropagation(); toggleMyItemsOptions('<?= htmlspecialchars($id) ?>')">
        <i data-lucide="more-horizontal"></i>
    </div>

    <div class="my-items-options-dropdown" id="my-items-options-dropdown-<?= htmlspecialchars($id) ?>">
        <div class="edit-item-btn" data-item-id="<?= htmlspecialchars($id) ?>">Edit</div>
        <div class="delete-item-btn" data-item-id="<?= htmlspecialchars($id) ?>"
            onclick="deleteMarketplaceItem(this, event)">Delete</div>
    </div>
    <?php endif; ?>
</div>