<?php
require_once(__DIR__ . "/../../models/MarketplaceItemImage.php");

$imageModel = new MarketplaceItemImage();
$image = $imageModel->first(['marketplace_item_id' => $id]);
$imageUrl = $image && isset($image->image_url) ? $image->image_url : null;
?>

<div class="marketplace-item-card" data-item-id="<?= htmlspecialchars($id) ?>">
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
        <div class="edit-item-btn" data-item-id="<?= htmlspecialchars($id) ?>"
            onclick="openEditMarketplaceItemModal('<?= htmlspecialchars($id) ?>')">
            Edit
        </div>
        <div class="delete-item-btn" data-item-id="<?= htmlspecialchars($id) ?>"
            onclick="deleteMarketplaceItem(this, event)">
            Delete
        </div>
    </div>

    <!-- Move modal INSIDE the card -->
    <div class="edit-marketplace-item-modal" onclick="closeEditMarketplaceItemModal()">
        <div class="edit-marketplace-item-content" onclick="event.stopPropagation();">
            <div class="sell-item-modal-header">
                <h2>Edit Listing</h2>
            </div>
            <form id="edit-sell-item-form-<?= htmlspecialchars($id) ?>" enctype="multipart/form-data"
                autocomplete="off">
                <input type="hidden" name="item_id" value="<?= htmlspecialchars($id) ?>">
                <div class="sell-item-modal-post-content">

                    <div>
                        <input type="text" name="title" id="edit-item-title-<?= htmlspecialchars($id) ?>"
                            value="<?= htmlspecialchars($title) ?>" placeholder="Item Title">
                        <div class="error-message" id="edit-sell-item-title-error"></div>
                    </div>

                    <div>
                        <textarea name="description" id="edit-item-description-<?= htmlspecialchars($id) ?>"
                            placeholder="Description"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                        <div class="error-message" id="edit-sell-item-description-error"></div>
                    </div>

                    <div>
                        <input type="number" name="price" id="edit-item-price-<?= htmlspecialchars($id) ?>"
                            value="<?= htmlspecialchars($price) ?>" placeholder="Price">
                        <div class="error-message" id="edit-sell-item-price-error"></div>
                    </div>

                    <div>
                        <select name="category_id" id="edit-item-category-<?= htmlspecialchars($id) ?>">
                            <option value="">Select Category</option>
                            <?php
                                if (isset($categories) && !empty($categories)) {
                                    foreach ($categories as $cat) {
                                        $selected = (isset($category_id) && $cat->id == $category_id) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($cat->id) . '" ' . $selected . '>' . htmlspecialchars($cat->name) . '</option>';
                                    }
                                }
                                ?>
                        </select>
                        <div class="error-message" id="edit-sell-item-category-error"></div>
                    </div>

                    <div>
                        <select name="status" id="edit-item-status-<?= htmlspecialchars($id) ?>">
                            <option value="Available" <?= ($status ?? '') === 'Available' ? 'selected' : '' ?>>Available
                            </option>
                            <option value="Sold" <?= ($status ?? '') === 'Sold' ? 'selected' : '' ?>>Sold</option>
                            <option value="Reserved" <?= ($status ?? '') === 'Reserved' ? 'selected' : '' ?>>Reserved
                            </option>
                            <option value="Not Available" <?= ($status ?? '') === 'Not Available' ? 'selected' : '' ?>>
                                Not Available</option>
                        </select>
                        <div class="error-message" id="edit-sell-item-status-error"></div>
                    </div>

                    <div class="sell-item-media-input">
                        <input type="file" name="media" id="edit-sell-item-media-input-<?= htmlspecialchars($id) ?>"
                            accept="image/*" style="display: none;">
                    </div>

                    <div class="edit-post-image" <?= empty($imageUrl) ? 'style="display:none;"' : '' ?>>
                        <img id="edit-sell-item-preview-img-<?= htmlspecialchars($id) ?>" class="edit-preview-img"
                            src="<?= htmlspecialchars($imageUrl ?? '') ?>" alt="Preview">
                    </div>

                    <div onclick="openEditItemImageSelector('<?= htmlspecialchars($id) ?>')"
                        class="sell-item-image-button">
                        <i data-lucide="image"></i>
                        <span><?= !empty($imageUrl) ? 'Change Image' : 'Add Image' ?></span>
                    </div>
                    <div class="error-message" id="edit-sell-item-media-error"></div>

                </div>
                <div class="sell-item-modal-actions">
                    <button type="button" class="sell-item-modal-button"
                        onclick="editMarketplaceItem('<?= htmlspecialchars($id) ?>')">Update Item</button>
                </div>
            </form>
            <div class="loading-spinner" id="edit-sell-item-loading-spinner" style="display:none;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
    <!-- End of modal -->
    <?php endif; ?>
</div>