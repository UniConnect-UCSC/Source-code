<?php component("navbar"); ?>
<div class="home-layout">
    <?php component("navPanel"); ?>
        <div class="feed">
            <div class="feed-header">
                <h2>My Items</h2>
            </div>
            <div class ="feed-items">
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
                "editUrl" => "/marketplace/editItem/{$item->id}",
                "deleteUrl" => "/marketplace/deleteItem/{$item->id}",
             ]); 
             }
            } else {
                echo "<p>You have not listed any items yet.</p>";
            }?>
        </div>
    </div>
    <?php component("widgetPanel"); ?>
</div>