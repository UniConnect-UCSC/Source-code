<?php component("navbar"); ?>  

<div class="home-layout">
    <?php component("navPanel"); ?>
    <div class="feed">
        <div class="feed-header">
            <h2>Marketplace</h2>
             <div class="feed-header-buttons">
                <a href="/marketplace/sellItem" class="btn-sellItem">Sell an Item</a>
                <a href="/marketplace/myItems" class="btn-myItems">My Items</a>
            </div>
        </div>
        <div class="feed-items">
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
    <?php component("widgetPanel"); ?>
</div>