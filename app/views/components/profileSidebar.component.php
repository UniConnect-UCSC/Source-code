<div class="profile-sidebar">
    <?php component("photosWidget", ["profileUser" => $profileUser ?? null]); ?>
    <?php component("friendsWidget", ["profileUser" => $profileUser ?? null]); ?>
    <!-- <?php component("achievementsWidget", ["profileUser" => $profileUser ?? null]); ?> -->
</div>