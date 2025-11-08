<?php

$photos = [
    (object)[
        'profile_picture' => 'https://images.unsplash.com/photo-1649861742672-20152f77c1f5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2128'
    ],
    (object)[
        'profile_picture' => 'https://images.unsplash.com/photo-1649861742672-20152f77c1f5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2128'
    ],
    (object)[
        'profile_picture' => 'https://images.unsplash.com/photo-1649861742672-20152f77c1f5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2128'
    ],
    (object)[
        'profile_picture' => 'https://images.unsplash.com/photo-1649861742672-20152f77c1f5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2128'
    ],
    (object)[
        'profile_picture' => 'https://images.unsplash.com/photo-1649861742672-20152f77c1f5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2128'
    ],
    (object)[
        'profile_picture' => 'https://images.unsplash.com/photo-1649861742672-20152f77c1f5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2128'
    ],
];
?>
<div class="photos-widget">
    <div>
        <h2>Photos</h2>
    </div>

    <div class="photos-grid">
        <?php foreach ($photos as $photo): ?>
        <a class="photo" href="/">
            <img src="<?= htmlspecialchars($photo->profile_picture) ?>" alt="Photo" />
        </a>
        <?php endforeach; ?>
    </div>

    <a class="see-all-photos" href="/photos">
        See All
    </a>
</div>