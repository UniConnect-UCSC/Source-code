<div class="boardings-container">
    <div class="boardings-header">
        <h2>Rooms & Boardings</h2>
        <div class="boardings-header-buttons">
            <a href="/boardings/postListing" class="btn-postListing">Post a Listing</a>
            <a href="/boardings/myListings" class="btn-myListings">My Listings</a>
        </div>
    </div>
    <div class="boarding-items">
        <?php
        if (!empty($rooms)) {
            foreach ($rooms as $room) {
                component("boardingCard", [
                    "id" => $room->id,
                    "rent" => $room->rent,
                    "occupancy" => $room->occupancy,
                    "status" => $room->status,
                    "location_id" => $room->location_id,
                    "image_url" => $room->image_url ?? null,
                    "created_at" => $room->created_at,
                ]);
            }
        } else {
            echo '<p class="no-listings">No boarding listings available at the moment.</p>';
        }
        ?>
    </div>
</div>