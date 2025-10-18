<?php
// Sample data for feed types

$feedTypes = [
    ['key' => 'global', 'title' => 'Global'],
    ['key' => 'university', 'title' => 'University']
];
?>

<div class="feed-type">
    <?php foreach ($feedTypes as $feedType): ?>
        <div class="">
            <span><?= htmlspecialchars($feedType['title']) ?></span>
        </div>
    <?php endforeach; ?>
</div>