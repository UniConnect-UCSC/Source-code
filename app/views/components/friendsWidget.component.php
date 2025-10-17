<?php

$friends = [
    (object)[
        'name' => 'Alice Johnson',
        'profile_picture' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hbnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&q=60&w=900'
    ],
    (object)[
        'name' => 'Bob Smith',
        'profile_picture' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hbnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&q=60&w=900'
    ],
    (object)[
        'name' => 'Charlie Brown',
        'profile_picture' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hbnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&q=60&w=900'
    ],
    (object)[
        'name' => 'Diana Prince',
        'profile_picture' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hbnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&q=60&w=900'
    ],
    (object)[
        'name' => 'Ethan Hunt',
        'profile_picture' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hbnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&q=60&w=900'
    ],
    (object)[
        'name' => 'Fiona Gallagher',
        'profile_picture' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hbnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&q=60&w=900'
    ],
];
?>
<div class="friends-widget">
    <div>
        <h2>Friends</h2>
    </div>

    <div class="friends-grid">
        <?php foreach ($friends as $friend): ?>
            <a class="friend" href="/">
                <img src="<?= htmlspecialchars($friend->profile_picture) ?>"
                    alt="<?= htmlspecialchars($friend->name) ?>'s profile picture" />
                <p><?= htmlspecialchars($friend->name) ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <a class="see-all-friends" href="/">
        See All
    </a>
</div>