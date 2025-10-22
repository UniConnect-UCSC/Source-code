<?php
// Sample data for feed types

$feedTypes = [
    ['key' => 'global', 'title' => 'Global'],
    ['key' => 'university', 'title' => 'University']
];
?>

<div class="feed-type">
    <?php foreach ($feedTypes as $feedType): ?>
    <button type="button" class="feed-type-item feed-type-item-inactive"
        data-feed="<?= htmlspecialchars($feedType['key']) ?>">
        <?= htmlspecialchars($feedType['title']) ?>
    </button>
    <?php endforeach; ?>
</div>