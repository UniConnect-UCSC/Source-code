<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/MarketPlaceCategories.php");

$itemsModel = new MarketplaceItem();
$items = $itemsModel->where([['student_id', '=', $_SESSION['user_id']]], null, null, ['created_at' => 'DESC']);

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
    <div class="my-items-container">

        <div class="my-items-header">
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
                        "description" => $item->description,
                        "categoryID" => $item->category_id,
                        "categoryName" => strtolower($categoryById[$item->category_id] ?? ''),
                        "status" => $item->status,
                        "created_at" => $item->created_at,
                        "contact_number" => $item->contact_number,
                        "myItems" => true
                    ]);
                }
            } else {
                echo "<p class='no-items-message'>You have not listed any items yet.</p>";
            } ?>
        </div>
    </div>
</div>