<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/MarketplaceCategories.php");

$itemsModel = new MarketplaceItem();
$items = $itemsModel->where([['student_id', '=', $_SESSION['user_id']]], null, null, ['created_at' => 'DESC']);

$categoryModel = new MarketplaceCategories();
$categories = $categoryModel->where([]);
?>

<?php component("navbar"); ?>
<div class="marketplace-layout">
    <?php component("navPanel"); ?>
    <div class="my-items-container">

        <div class="">
            <h2>My Items</h2>
        </div>

        <div class="my-items-list">
            <?php
            if (!empty($items)) {
                foreach ($items as $item) {
                    component("marketplaceItem", [
                        "id" => $item->id,
                        "title" => $item->title,
                        "price" => $item->price,
                        "status" => $item->status,
                        "created_at" => $item->created_at,
                        "categories" => $categories,
                        "category_id" => $item->category_id,
                        "description" => $item->description,
                        "myItems" => true
                    ]);
                }
            } else {
                echo "<p>You have not listed any items yet.</p>";
            } ?>
        </div>
    </div>
</div>