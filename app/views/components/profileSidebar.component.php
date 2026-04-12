<div class="profile-sidebar">
    <?php component("photosWidget", ["profileUser" => $profileUser ?? null]); ?>
    <?php component("friendsWidget"); ?>
    <?php component("achievementsWidget"); ?>
</div>