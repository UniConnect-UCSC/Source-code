<?php $globalPosts = $globalPosts ?? []; ?>

<div class="posts-table-wrapper">
    <div class="posts-table-header">
        <h2>Posts</h2>

        <div class="posts-table-controls">
            <div class="posts-toggle" role="tablist" aria-label="Post Type">
                <button type="button" class="posts-toggle-btn active" data-type="global">Global</button>
                <button type="button" class="posts-toggle-btn" data-type="university">University</button>
            </div>

            <input type="text" id="post-search" class="post-search-input" placeholder="Search posts...">
        </div>
    </div>

    <table class="posts-table">
        <thead>
            <tr>
                <th>Post ID</th>
                <th>User ID</th>
                <th>University</th>
                <th>Caption</th>
                <th>Reported</th>
                <th>Deleted</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="posts-table-body">
            <?php foreach ($globalPosts as $post): ?>
                <?php
                $isReported = !empty($post->reported);
                $isDeleted = !empty($post->deleted_at);
                ?>
                <tr>
                    <td><?= htmlspecialchars($post->id) ?></td>
                    <td><?= htmlspecialchars($post->user_id) ?></td>
                    <td><span class="type-pill">Global</span></td>
                    <td class="caption-cell"><?= htmlspecialchars($post->caption ?? '-') ?></td>
                    <td>
                        <span class="status-pill <?= $isReported ? 'status-yes' : 'status-no' ?>">
                            <?= $isReported ? 'Reported' : 'Not reported' ?>
                        </span>
                    </td>
                    <td class="deleted-status-cell">
                        <span class="status-pill <?= $isDeleted ? 'status-yes' : 'status-no' ?>">
                            <?= $isDeleted ? 'Deleted' : 'Active' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars(date('M d, Y', strtotime($post->created_at))) ?></td>
                    <td>
                        <button class="table-btn delete-btn" onclick="softDeletePost(<?= (int)$post->id ?>, 'global', this)"
                            <?= $isDeleted ? 'disabled' : '' ?>>
                            <?= $isDeleted ? 'Deleted' : 'Delete' ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>