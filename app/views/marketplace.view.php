<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/MarketplaceCategories.php");

$itemsModels = new MarketplaceItem();
$items = $itemsModels->where([], null, null, ['created_at' => 'DESC']);

$categoryModel = new MarketplaceCategories();
$categories = $categoryModel->where([]);
?>

<?php component("navbar"); ?>

<div class="marketplace-layout">
    <?php component("navPanel"); ?>
    <div class="marketplace-container">
        <div class="market-items-header">
            <h2>Marketplace</h2>
            <div class="market-items-header-buttons">
                <div class="btn-sellItem" onclick="openSellItemModal()">Sell an Item</div>
                <a href="/marketplace/myItems" class="btn-myItems">My Items</a>
            </div>
        </div>
        <div class="market-items">
            <?php
            if (!empty($items)) {
                foreach ($items as $item) {
                    component("marketplaceItem", [
                        "id" => $item->id,
                        "studentID" => $item->student_id,
                        "title" => $item->title,
                        "description"   => $item->description,
                        "price" => $item->price,
                        "categoryID" => $item->category_id,
                        "categories" => $categories,
                        "status" => $item->status,
                        "created_at" => $item->created_at,
                    ]);
                }
            }
            ?>
        </div>
    </div>
</div>


<div class="sell-item-modal" id="sell-item-modal" onclick="closeSellItemModal()">
    <div class="sell-item-modal-content" onclick="event.stopPropagation()">
        <div class="sell-item-modal-header">
            <h2>Sell an Item</h2>
        </div>
        <form id="sell-item-form" enctype="multipart/form-data" autocomplete="off">
            <div class="sell-item-modal-post-content">

                <div>
                    <input type="text" name="title" id="item-title" placeholder="Item Title">
                    <div class="error-message" id="sell-item-title-error"></div>
                </div>

                <div>
                    <textarea name="description" id="item-description" placeholder="Description"></textarea>
                    <div class="error-message" id="sell-item-description-error"></div>
                </div>

                <div>
                    <input type="number" name="price" id="item-price" placeholder="Price" min="0">
                    <div class="error-message" id="sell-item-price-error"></div>
                </div>

                <div>
                    <select name="category_id" id="item-category">
                        <option value="">Select Category</option>
                        <?php
                        foreach ($categories as $cat) {
                            echo '<option value="' . htmlspecialchars($cat->id) . '">' . htmlspecialchars($cat->name) . '</option>';
                        }
                        ?>
                    </select>

                    <div class="error-message" id="sell-item-category-error"></div>
                </div>

                <div class="sell-item-media-input">
                    <input id="sell-item-media-input" name="media" type="file" accept="image/*" />
                </div>

                <div class="sell-item-image-preview">
                    <img id="sell-item-preview-img" src="">
                </div>

                <div onclick="openSellItemImageSelector()" class="sell-item-image-button">
                    <i data-lucide="image"></i>
                    Photo
                </div>
                <div class="error-message" id="sell-item-media-error"></div>


            </div>
            <div class="sell-item-modal-actions">
                <button type="submit" class="sell-item-modal-button">Sell Item</button>
            </div>
        </form>
        <div class="loading-spinner" id="sell-item-loading-spinner" style="display:none;">
            <div class="spinner"></div>
        </div>
    </div>
</div>