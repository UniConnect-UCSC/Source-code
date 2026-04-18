<?php component("navbar"); ?>

<?php
$itemTitle = (string)($item->title ?? 'Untitled Item');
$itemDescription = trim((string)($item->description ?? ''));
$itemPrice = (float)($item->price ?? 0);
$itemStatus = strtolower(trim((string)($item->status ?? 'available')));
$allowedStatuses = ['available', 'sold', 'reserved'];
if (!in_array($itemStatus, $allowedStatuses, true)) {
    $itemStatus = 'available';
}

$postedDate = !empty($item->created_at) ? date("F j, Y", strtotime((string)$item->created_at)) : '';
$imageCount = !empty($imageUrls) ? count($imageUrls) : 0;
?>

<div class="marketplace-layout marketplace-details-page">
 

    <div class="marketplace-container">
        <div class="market-items-header">
            <h2>Item Details</h2>
            <div class="market-items-header-buttons">
                <a href="/marketplace" class="btn-myItems">Back to Marketplace</a>
            </div>
        </div>

        <article class="marketplace-details-card">
            <div class="marketplace-details-media">
                <?php if (!empty($categoryName)): ?>
                    <div class="marketplace-details-category-badge">
                        <?= htmlspecialchars((string)$categoryName) ?>
                    </div>
                <?php endif; ?>

                <div class="marketplace-details-track" id="marketplace-details-track">
                    <?php if (!empty($imageUrls)): ?>
                        <?php foreach ($imageUrls as $imageUrl): ?>
                            <img class="marketplace-details-image" src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($itemTitle) ?>" loading="lazy" />
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="marketplace-details-no-image marketplace-details-image">No Image Available</div>
                    <?php endif; ?>
                </div>

                <?php if ($imageCount > 1): ?>
                    <button type="button" class="marketplace-details-nav marketplace-details-prev" id="marketplace-details-prev" aria-label="Previous image">&#10094;</button>
                    <button type="button" class="marketplace-details-nav marketplace-details-next" id="marketplace-details-next" aria-label="Next image">&#10095;</button>
                <?php endif; ?>
            </div>

            <div class="marketplace-details-content marketplace-item-details">
                <h3 class="marketplace-item-title"><?= htmlspecialchars($itemTitle) ?></h3>

                <p class="marketplace-item-price">LKR <?= number_format($itemPrice, 2) ?></p>

                <?php if ($itemDescription !== ''): ?>
                    <p class="marketplace-description marketplace-description-full">
                        <?= htmlspecialchars($itemDescription) ?>
                    </p>
                <?php endif; ?>

                <?php if ($postedDate !== ''): ?>
                    <p class="marketplace-item-date"><?= htmlspecialchars($postedDate) ?></p>
                <?php endif; ?>

                <div class="marketplace-details-actions">
                    <button type="button" class="marketplace-contact-seller" id="marketplace-contact-reveal-btn" aria-expanded="false">
                        <i data-lucide="phone"></i>
                        Show Contact
                    </button>
                    <p class="marketplace-contact-number" id="marketplace-contact-number" hidden>
                        <?= htmlspecialchars((string)($sellerContactNumber ?: 'Contact number not added')) ?>
                    </p>
                </div>
            </div>
        </article>
    </div>
</div>

<div id="marketplace-photo-lightbox" class="marketplace-photo-lightbox" hidden>
    <button type="button" class="marketplace-photo-lightbox-nav marketplace-photo-lightbox-prev" id="marketplace-photo-lightbox-prev" aria-label="Previous photo">&#10094;</button>
    <button type="button" class="marketplace-photo-lightbox-close" id="marketplace-photo-lightbox-close" aria-label="Close enlarged photo">&times;</button>
    <img id="marketplace-photo-lightbox-image" class="marketplace-photo-lightbox-image" src="" alt="Enlarged item photo" />
    <button type="button" class="marketplace-photo-lightbox-nav marketplace-photo-lightbox-next" id="marketplace-photo-lightbox-next" aria-label="Next photo">&#10095;</button>
    <div class="marketplace-photo-lightbox-thumbs" id="marketplace-photo-lightbox-thumbs" aria-label="Photo thumbnails"></div>
</div>

<script src="/assets/js/marketplaceDetails.js"></script>
