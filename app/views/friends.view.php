<?php component("navbar"); ?>

<div class="friends-layout">
    <?php
    component("navPanel");
    component("friends", ['friends' => $friends ?? []]);
    // component("friendSuggestions");
    component("friendRequests", ['friendRequests' => $friendRequests ?? []]);
    ?>
</div>