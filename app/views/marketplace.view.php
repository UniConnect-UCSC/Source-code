<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/MarketPlaceCategories.php");


$itemsModels = new MarketplaceItem();
$items = $itemsModels->where([[ 'status', '!=', 'sold' ]], null, null, ['created_at' => 'DESC']);

$categoryModel = new MarketPlaceCategories();
$categories = $categoryModel->where([]);

$categoryById = [];
if (!empty($categories)) {
    foreach ($categories as $cat) {
        $categoryById[$cat->id] = $cat->name;
    }
}


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
                
                
<button type="button" class="marketplace-saved-toggle" id="marketplace-saved-open">
  <i data-lucide="bookmark"></i>
  Saved Items
</button>
                
            </div>
        </div>


        <div class="marketplace-filterbar">
            <div class="marketplace-category-pills">
                <button type="button" class="marketplace-category-pill is-active" data-category="all">All Items</button>
                <button type="button" class="marketplace-category-pill" data-category="electronics">Electronics</button>
                <button type="button" class="marketplace-category-pill" data-category="books">Books</button>
                <button type="button" class="marketplace-category-pill" data-category="furniture">Furniture</button>
                <button type="button" class="marketplace-category-pill" data-category="clothing">Clothing</button>
                <button type="button" class="marketplace-category-pill" data-category="sports">Sports</button>
                <button type="button" class="marketplace-category-pill" data-category="other">Other</button>
            </div>

            <div class="marketplace-search">
                <i data-lucide="search"></i>
                <input id="marketplace-search-input" type="search" placeholder="Search items..." autocomplete="off" />
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
                        "description" => $item->description,
                        "price" => $item->price,
                        "categoryID" => $item->category_id,
                        "categoryName" => strtolower($categoryById[$item->category_id] ?? ''),
                        "status" => $item->status,
                        "created_at" => $item->created_at,
                    ]);
                }
            }
            ?>


            <div id="marketplace-no-results" class="marketplace-no-results" style="display:none;">
                No items found matching your criteria.
            </div>
        </div>


    </div>

   
<div id="marketplace-saved-backdrop" class="marketplace-saved-backdrop"></div>

<aside id="marketplace-saved-sidebar" class="marketplace-saved-sidebar">
  <div class="marketplace-saved-sidebar-header">
    <h3>Saved Items</h3>
    <button type="button" class="marketplace-saved-close" id="marketplace-saved-close" aria-label="Close saved items">
      <i data-lucide="x"></i>
    </button>
  </div>

  <div id="marketplace-saved-empty" class="marketplace-saved-empty">
    No saved items yet.
  </div>

  <div id="marketplace-saved-list" class="marketplace-saved-list"></div>
</aside>
    
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
                    <select name="category_id" id="item-category" required>
                        <option value="" disabled selected>Select Category</option>
                        <?php
                        foreach ($categories as $cat) {
                            echo '<option value="' . htmlspecialchars($cat->id) . '">' . htmlspecialchars($cat->name) . '</option>';
                        }
                        ?>
                    </select>

                    <div class="error-message" id="sell-item-category-error"></div>
                </div>

                <div>
                    <select name="status" id="item-status" required>
                        <option value="" disabled selected>Select Status</option>
                        <option value="available">Available</option>
                        <option value="reserved">Reserved</option>
                    </select>
                    <div class="error-message" id="sell-item-status-error"></div>
                </div>
                <div>

                </div>
                <input type="text" name="contact_number" id="item-contact-number" placeholder="Contact Number">
                <div class="error-message" id="sell-item-contact-number-error"></div>

                <div class="sell-item-media-input">
                    <input id="sell-item-media-input" name="media[]" type="file" accept="image/*" multiple />
                </div>

                <div class="sell-item-image-preview">
                    <div id="sell-item-preview-grid"></div>
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