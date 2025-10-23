<?php component("navbar"); ?>

<div class="marketplace-layout">
    <?php component("navPanel"); ?>
    <div class="marketplace-container">
        <div class="market-items-header">
            <h2>Marketplace</h2>
            <div class="market-items-header-buttons">
                <a href="/marketplace/sellItem" class="btn-sellItem">Sell an Item</a>
                <a href="/marketplace/myItems" class="btn-myItems">My Items</a>
            </div>
        </div>
        <div class="market-items">
            <?php
            if (!empty($items)) {
                foreach ($items as $item) {
                    component("marketplaceItem", [
                        "id" => $item->id,
                        "title" => $item->title,
                        "price" => $item->price,
                        "status" => $item->status,
                        //              "image" => $item->image,
                        "created_at" => $item->created_at,
                    ]);
                }
            }
            ?>
        </div>
    </div>
</div>