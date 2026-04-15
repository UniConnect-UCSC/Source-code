<?php
require_once(__DIR__ . "/../models/BoardingRoom.php");
require_once(__DIR__ . "/../models/BoardingLocations.php");

$roomsModel = new BoardingRoom();
$rooms = $roomsModel->where([['student_id', '=', $_SESSION['user_id']]], null, null, ['created_at' => 'DESC']);

$locationModel = new BoardingLocations();
$locations = $locationModel->where([]);
?>

<?php component("navbar"); ?>
<div class="boardings-layout">
    <?php component("navPanel"); ?>
    <div class="my-listings-container">

        <div class="my-listings-header">
            <h2>My Listings</h2>

            
        </div>

        

        <div class="my-listings-list">
            <?php
            if (!empty($rooms)) {
                foreach ($rooms as $room) {
                    component("boardingCard", [
                        "id" => $room->id,
                        "rent" => $room->rent,
                        "occupancy" => $room->occupancy,
                        "gender" => $room->gender ,
                        "location_id" => $room->location_id,
                        "description" => $room->description ?? null,
                        "status" => $room->status,
                        "category" => $room->category,      // ADD THIS
                        "facilities" => $room->facilities,  // ADD THIS
                        "contact_number" => $room->contact_number ?? null,
                        "image_url" => $room->image_url ?? null,
                        "created_at" => $room->created_at,
                        "myListings" => true
                    ]);
                }
            } else {
                echo "<p class='no-listings-message'>You have not posted any boarding listings yet.</p>";
            } ?>
        </div>
    </div>
</div>