<div class="achievements-widget">

    <?php
    // Sample data for achievements
    $achievements = [
        ['icon' => 'award', 'title' => 'Top Contributor'],
        ['icon' => 'star', 'title' => '5-Star Rating'],
        ['icon' => 'trophy', 'title' => 'Winner of Hackathon'],
        ['icon' => 'medal', 'title' => 'Community Helper'],
        ['icon' => 'heart', 'title' => 'Most Liked Post'],
        ['icon' => 'lightbulb', 'title' => 'Innovator Award'],
        ['icon' => 'rocket', 'title' => 'Fast Responder'],
        ['icon' => 'shield', 'title' => 'Trusted Member'],
    ];
    ?>

    <h2>Achievements</h2>
    <ul class="achievements-list">
        <?php foreach ($achievements as $achievement): ?>
            <li>
                <i data-lucide="<?= htmlspecialchars($achievement['icon']) ?>"></i>
                <span><?= htmlspecialchars($achievement['title']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>