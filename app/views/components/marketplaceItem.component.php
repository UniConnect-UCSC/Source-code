
<div class="marketplace-item-card">
    <div class="marketplace-item-image">
        <?php if (!empty($image)): ?>
            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($title ?? '') ?>" />
        <?php endif; ?>
    </div>
    <div class="marketplace-item-details">
        <h3 class="marketplace-item-title"><?= htmlspecialchars($title ?? 'No Title') ?></h3>
        <p class="marketplace-item-price">Rs. <?= htmlspecialchars($price ?? 'N/A') ?></p>
        <span class="marketplace-item-status <?= strtolower($status ?? 'available') ?>">
            <?= htmlspecialchars($status ?? 'Available') ?>
        </span>
        <p class="marketplace-item-date">
            <?php if (!empty($created_at)): ?>
                <?= date('M d, Y', strtotime($created_at)) ?>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </p>
    </div>
    <div class="marketplace-item-footer">
        <?php if (!empty($editUrl) || !empty($deleteUrl)): ?>
            <div class="marketplace-item-footer-actions">
                <?php if (!empty($editUrl)): ?>
                    <a href="<?= htmlspecialchars($editUrl) ?>" class="btn edit-btn">Edit</a>
                <?php endif; ?>
                <?php if (!empty($deleteUrl)): ?>
                    <form action="<?= htmlspecialchars($deleteUrl) ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                        <button type="submit" class="btn delete-btn">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <a href="/marketplace/item/<?= urlencode($id ?? '') ?>" class="marketplace-item-action">View Details</a>
        <?php endif; ?>
    </div>    
</div>